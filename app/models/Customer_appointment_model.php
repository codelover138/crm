<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_appointment_model extends CI_Model {

    const APPOINTMENT_PREFIX = 'APT';
    const TABLE = 'customer_appointments';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Check if customer has any active appointment (not completed/cancelled and date not passed).
     * Returns true if they have an active appointment that prevents booking a new one.
     */
    public function hasActiveAppointment($customer_id) {
        $today = date('Y-m-d');
        $this->db->from(self::TABLE)
            ->where('customer_id', (int) $customer_id)
            ->where_in('status', array('pending', 'confirmed', 'rescheduled'))
            ->where('preferred_date >=', $today);
        return $this->db->count_all_results() > 0;
    }

    /**
     * Whether the customer has any appointment that is pending (not yet confirmed/cancelled/completed).
     * Optional: limit to 1 pending appointment at a time.
     */
    public function hasPendingAppointment($customer_id) {
        $this->db->from(self::TABLE)
            ->where('customer_id', (int) $customer_id)
            ->where('status', 'pending');
        return $this->db->count_all_results() > 0;
    }

    /**
     * Get appointments for a customer, newest first.
     */
    public function getByCustomerId($customer_id, $limit = 50) {
        $this->db->select('id, appointment_code, appointment_type, subject, description, preferred_date, preferred_time, duration_minutes, status, confirmed_date, confirmed_time, created_at')
            ->from(self::TABLE)
            ->where('customer_id', (int) $customer_id)
            ->order_by('created_at', 'DESC')
            ->limit($limit);
        $q = $this->db->get();
        return $q->num_rows() > 0 ? $q->result() : array();
    }

    /**
     * Get upcoming appointments (pending or confirmed, future dates only).
     */
    public function getUpcomingByCustomerId($customer_id, $limit = 10) {
        $today = date('Y-m-d');
        $this->db->select('id, appointment_code, appointment_type, subject, description, preferred_date, preferred_time, duration_minutes, status, confirmed_date, confirmed_time, created_at')
            ->from(self::TABLE)
            ->where('customer_id', (int) $customer_id)
            ->where_in('status', array('pending', 'confirmed', 'rescheduled'))
            ->where('preferred_date >=', $today)
            ->order_by('preferred_date', 'ASC')
            ->order_by('preferred_time', 'ASC')
            ->limit($limit);
        $q = $this->db->get();
        return $q->num_rows() > 0 ? $q->result() : array();
    }

    /**
     * Get past appointments (completed or cancelled).
     */
    public function getPastByCustomerId($customer_id, $limit = 20) {
        $this->db->select('id, appointment_code, appointment_type, subject, description, preferred_date, preferred_time, duration_minutes, status, confirmed_date, confirmed_time, created_at')
            ->from(self::TABLE)
            ->where('customer_id', (int) $customer_id)
            ->where_in('status', array('completed', 'cancelled'))
            ->order_by('created_at', 'DESC')
            ->limit($limit);
        $q = $this->db->get();
        return $q->num_rows() > 0 ? $q->result() : array();
    }

    /**
     * Add an appointment. Generates appointment_code: {prefix}-{customer_code_or_id}-{Ymd}-{seq}.
     */
    public function add($customer_id, $data, $customer_code = null) {
        $customer_id = (int) $customer_id;
        
        // Validate required fields
        $required = array('appointment_type', 'subject', 'preferred_date', 'preferred_time');
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return array('success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.');
            }
        }

        // Enforce one active appointment at a time (pending/confirmed/rescheduled with future date)
        if ($this->hasActiveAppointment($customer_id)) {
            return array('success' => false, 'message' => 'You cannot book a new appointment until your current appointment is completed or the date has passed.');
        }

        // Generate appointment code
        $slug = $customer_code !== null && $customer_code !== '' 
            ? preg_replace('/[^a-zA-Z0-9\-]/', '', $customer_code) 
            : (string) $customer_id;
        $date_part = date('Ymd');
        $seq = $this->getNextSequenceForCustomer($customer_id, $date_part);
        $appointment_code = self::APPOINTMENT_PREFIX . '-' . $slug . '-' . $date_part . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

        // Prepare data
        $insert_data = array(
            'customer_id'       => $customer_id,
            'appointment_code'  => $appointment_code,
            'appointment_type'  => trim($data['appointment_type']),
            'subject'           => trim($data['subject']),
            'description'       => isset($data['description']) ? trim($data['description']) : '',
            'preferred_date'    => $data['preferred_date'],
            'preferred_time'    => $data['preferred_time'],
            'duration_minutes'  => isset($data['duration_minutes']) ? (int)$data['duration_minutes'] : 30,
            'status'            => 'pending',
        );

        if ($this->db->insert(self::TABLE, $insert_data)) {
            return array(
                'success'           => true,
                'id'                => (int) $this->db->insert_id(),
                'appointment_code'  => $appointment_code,
                'created_at'        => date('Y-m-d H:i:s'),
            );
        }
        return array('success' => false, 'message' => 'Failed to save appointment.');
    }

    /**
     * Cancel an appointment (customer can only cancel pending appointments).
     */
    public function cancel($appointment_id, $customer_id) {
        $this->db->where('id', (int) $appointment_id)
            ->where('customer_id', (int) $customer_id)
            ->where('status', 'pending');
        
        if ($this->db->update(self::TABLE, array('status' => 'cancelled'))) {
            return array('success' => true, 'message' => 'Appointment cancelled successfully.');
        }
        return array('success' => false, 'message' => 'Unable to cancel appointment. Only pending appointments can be cancelled.');
    }

    /**
     * Get next sequence number for this customer for today (for same-day appointment codes).
     */
    private function getNextSequenceForCustomer($customer_id, $date_part) {
        $this->db->select('id')
            ->from(self::TABLE)
            ->where('customer_id', $customer_id)
            ->like('appointment_code', '-' . $date_part . '-');
        $count = $this->db->count_all_results();
        return $count + 1;
    }

    /**
     * Get appointment types for dropdown.
     */
    public function getAppointmentTypes() {
        return array(
            'support'       => 'Technical Support',
            'consultation'  => 'Consultation',
            'demo'          => 'Product Demo',
            'training'      => 'Training Session',
            'other'         => 'Other',
        );
    }

    /**
     * Get duration options for dropdown.
     */
    public function getDurationOptions() {
        return array(
            30  => '30 minutes',
            60  => '1 hour',
            90  => '1.5 hours',
            120 => '2 hours',
        );
    }

    /**
     * Get a single appointment by ID (for admin).
     */
    public function getById($id) {
        $q = $this->db->get_where(self::TABLE, array('id' => (int) $id), 1);
        return $q->num_rows() > 0 ? $q->row() : null;
    }

    /**
     * Get all appointments for admin list (with customer name).
     */
    public function getAllForAdmin() {
        $this->db->select(self::TABLE . '.id, ' . self::TABLE . '.appointment_code, ' . self::TABLE . '.appointment_type, ' . self::TABLE . '.subject, ' . self::TABLE . '.preferred_date, ' . self::TABLE . '.preferred_time, ' . self::TABLE . '.duration_minutes, ' . self::TABLE . '.status, ' . self::TABLE . '.created_at, ' . self::TABLE . '.customer_id, companies.name as customer_name, companies.email as customer_email')
            ->from(self::TABLE)
            ->join('companies', 'companies.id = ' . self::TABLE . '.customer_id', 'left')
            ->order_by(self::TABLE . '.preferred_date', 'DESC')
            ->order_by(self::TABLE . '.created_at', 'DESC');
        $q = $this->db->get();
        return $q->num_rows() > 0 ? $q->result() : array();
    }

    /**
     * Update an appointment (admin).
     */
    public function update($id, $data) {
        $allowed = array('appointment_type', 'subject', 'description', 'preferred_date', 'preferred_time', 'duration_minutes', 'status', 'confirmed_date', 'confirmed_time');
        $update = array();
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $update[$key] = is_string($data[$key]) ? trim($data[$key]) : $data[$key];
            }
        }
        if (empty($update)) {
            return false;
        }
        $this->db->where('id', (int) $id);
        return $this->db->update(self::TABLE, $update);
    }

    /**
     * Delete an appointment (admin).
     */
    public function delete($id) {
        $this->db->where('id', (int) $id);
        return $this->db->delete(self::TABLE);
    }

    /**
     * Get status options for dropdown.
     */
    public function getStatusOptions() {
        return array(
            'pending'    => 'Pending',
            'confirmed'  => 'Confirmed',
            'rescheduled'=> 'Rescheduled',
            'completed'  => 'Completed',
            'cancelled'  => 'Cancelled',
        );
    }
}
