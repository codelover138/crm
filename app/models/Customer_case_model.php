<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Customer cases (Open Case / Case History) for customer dashboard.
 */
class Customer_case_model extends CI_Model {

    const CASE_PREFIX = 'CS';
    const TABLE = 'customer_cases';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Whether the customer has any case that is not closed (open or in_progress).
     * New cases cannot be created until the current one is resolved (closed).
     */
    public function hasOpenCase($customer_id) {
        $this->db->from(self::TABLE)
            ->where('customer_id', (int) $customer_id)
            ->where('status !=', 'closed');
        return $this->db->count_all_results() > 0;
    }

    /**
     * Get cases for a customer, newest first.
     */
    public function getByCustomerId($customer_id, $limit = 50) {
        $this->db->select('id, case_code, details, status, created_at')
            ->from(self::TABLE)
            ->where('customer_id', (int) $customer_id)
            ->order_by('created_at', 'DESC')
            ->limit($limit);
        $q = $this->db->get();
        return $q->num_rows() > 0 ? $q->result() : array();
    }

    /**
     * Add a case. Generates case_code: {prefix}-{customer_code_or_id}-{Ymd}-{seq}.
     */
    public function add($customer_id, $details, $customer_code = null) {
        $customer_id = (int) $customer_id;
        $details = trim($details);
        if ($details === '') {
            return array('success' => false, 'message' => 'Details are required.');
        }
        if ($this->hasOpenCase($customer_id)) {
            return array('success' => false, 'message' => 'You cannot open a new case until your current case is resolved (closed).');
        }

        $slug = $customer_code !== null && $customer_code !== '' 
            ? preg_replace('/[^a-zA-Z0-9\-]/', '', $customer_code) 
            : (string) $customer_id;
        $date_part = date('Ymd');
        $seq = $this->getNextSequenceForCustomer($customer_id, $date_part);
        $case_code = self::CASE_PREFIX . '-' . $slug . '-' . $date_part . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

        $data = array(
            'customer_id' => $customer_id,
            'case_code'   => $case_code,
            'details'     => $details,
            'status'      => 'open',
        );
        if ($this->db->insert(self::TABLE, $data)) {
            return array(
                'success'   => true,
                'id'        => (int) $this->db->insert_id(),
                'case_code' => $case_code,
                'created_at'=> date('Y-m-d H:i:s'),
            );
        }
        return array('success' => false, 'message' => 'Failed to save case.');
    }

    /**
     * Next sequence number for this customer for today (for same-day case codes).
     */
    private function getNextSequenceForCustomer($customer_id, $date_part) {
        $this->db->select('id')
            ->from(self::TABLE)
            ->where('customer_id', $customer_id)
            ->like('case_code', '-' . $date_part . '-');
        $count = $this->db->count_all_results();
        return $count + 1;
    }
}
