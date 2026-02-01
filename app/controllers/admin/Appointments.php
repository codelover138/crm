<?php defined('BASEPATH') or exit('No direct script access allowed');

class Appointments extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer || $this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('sma', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->model('customer_appointment_model');
    }

    public function index()
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('appointments')));
        $meta = array('page_title' => lang('appointments'), 'bc' => $bc);
        $this->page_construct('appointments/index', $meta, $this->data);
    }

    public function getAppointments()
    {
        $this->sma->checkPermissions('index');

        $edit_link = anchor('admin/appointments/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit'), 'class="sledit"');
        $delete_link = "<a href='#' class='po' title='<b>" . lang("Delete") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('appointments/delete/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete') . "</a>";

        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">';
        if ($this->sma->actionPermissions('edit', 'appointments')) {
            $action .= '<li>' . $edit_link . '</li>';
        }
        if ($this->sma->actionPermissions('delete', 'appointments')) {
            $action .= '<li>' . $delete_link . '</li>';
        }
        $action .= '</ul></div></div>';

        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('customer_appointments') . ".id as id, appointment_code, appointment_type, subject, preferred_date, preferred_time, " . $this->db->dbprefix('customer_appointments') . ".status, " . $this->db->dbprefix('companies') . ".name as customer_name")
            ->from('customer_appointments')
            ->join('companies', 'companies.id = customer_appointments.customer_id', 'left')
            ->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }

    public function add()
    {
        $this->sma->checkPermissions('add', true);

        $this->form_validation->set_rules('customer_id', lang("customer"), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('appointment_type', lang("appointment_type"), 'required');
        $this->form_validation->set_rules('subject', lang("subject"), 'required');
        $this->form_validation->set_rules('preferred_datetime', lang("date"), 'required');

        if ($this->form_validation->run() == true) {
            $customer_id = (int) $this->input->post('customer_id');
            $customer = $this->site->getCompanyByID($customer_id);
            $customer_code = $customer ? $customer->cf1 : null;
            // Same as communication: fld() converts UI datetime to Y-m-d H:i:s, then split
            $preferred_datetime = $this->sma->fld(trim($this->input->post('preferred_datetime')));
            $preferred_date = $preferred_datetime ? date('Y-m-d', strtotime($preferred_datetime)) : '';
            $preferred_time = $preferred_datetime ? date('H:i', strtotime($preferred_datetime)) : '';
            $data = array(
                'appointment_type'  => $this->input->post('appointment_type'),
                'subject'           => $this->input->post('subject'),
                'description'       => $this->input->post('description'),
                'preferred_date'    => $preferred_date,
                'preferred_time'    => $preferred_time,
                'duration_minutes'  => (int) $this->input->post('duration_minutes') ?: 30,
            );
            $result = $this->customer_appointment_model->add($customer_id, $data, $customer_code);
            if (!empty($result['success'])) {
                $this->session->set_flashdata('message', lang('data_added'));
                admin_redirect('appointments');
            } else {
                $this->session->set_flashdata('error', $result['message']);
            }
        }

        $this->data['customers'] = $this->site->getAllCompanies('customer');
        $this->data['appointment_types'] = $this->customer_appointment_model->getAppointmentTypes();
        $this->data['duration_options'] = $this->customer_appointment_model->getDurationOptions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('appointments'), 'page' => lang('appointments')), array('link' => '#', 'page' => lang('add_appointment')));
        $meta = array('page_title' => lang('add_appointment'), 'bc' => $bc);
        $this->page_construct('appointments/add', $meta, $this->data);
    }

    public function edit($id = null)
    {
        $this->sma->checkPermissions('edit', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $appointment = $this->customer_appointment_model->getById($id);
        if (!$appointment) {
            $this->session->set_flashdata('error', lang('no_data_found'));
            admin_redirect('appointments');
        }

        $this->form_validation->set_rules('appointment_type', lang("appointment_type"), 'required');
        $this->form_validation->set_rules('subject', lang("subject"), 'required');
        $this->form_validation->set_rules('preferred_datetime', lang("date"), 'required');
        $this->form_validation->set_rules('confirmed_time', lang("confirmed_time"), 'callback_valid_time_format');
        $this->form_validation->set_rules('status', lang("status"), 'required');

        if ($this->form_validation->run() == true) {
            // Same as communication: fld() converts preferred_datetime, then split
            $preferred_datetime = $this->sma->fld(trim($this->input->post('preferred_datetime')));
            $preferred_date = $preferred_datetime ? date('Y-m-d', strtotime($preferred_datetime)) : '';
            $preferred_time = $preferred_datetime ? date('H:i', strtotime($preferred_datetime)) : '';
            $confirmed_date_raw = trim($this->input->post('confirmed_date'));
            $confirmed_date = null;
            if ($confirmed_date_raw) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $confirmed_date_raw)) {
                    $confirmed_date = $confirmed_date_raw;
                } else {
                    $confirmed_date = $this->sma->fsd($confirmed_date_raw);
                    if ($confirmed_date === '0000-00-00') {
                        $confirmed_date = null;
                    }
                }
            }
            $confirmed_time = $this->input->post('confirmed_time') ? $this->_normalize_time($this->input->post('confirmed_time')) : null;
            $data = array(
                'appointment_type'   => $this->input->post('appointment_type'),
                'subject'            => $this->input->post('subject'),
                'description'        => $this->input->post('description'),
                'preferred_date'     => $preferred_date,
                'preferred_time'     => $preferred_time,
                'duration_minutes'   => (int) $this->input->post('duration_minutes') ?: 30,
                'status'             => $this->input->post('status'),
                'confirmed_date'     => $confirmed_date,
                'confirmed_time'     => $confirmed_time,
            );
            if ($this->customer_appointment_model->update($id, $data)) {
                $this->session->set_flashdata('message', lang('data_updated'));
                admin_redirect('appointments');
            }
        }

        $this->data['appointment'] = $appointment;
        $this->data['customer'] = $this->site->getCompanyByID($appointment->customer_id);
        $this->data['appointment_types'] = $this->customer_appointment_model->getAppointmentTypes();
        $this->data['duration_options'] = $this->customer_appointment_model->getDurationOptions();
        $this->data['status_options'] = $this->customer_appointment_model->getStatusOptions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('appointments'), 'page' => lang('appointments')), array('link' => '#', 'page' => lang('edit')));
        $meta = array('page_title' => lang('edit'), 'bc' => $bc);
        $this->page_construct('appointments/edit', $meta, $this->data);
    }

    private function _normalize_time($str)
    {
        if (empty($str) || !preg_match('/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/', trim($str), $m)) {
            return $str;
        }
        return sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
    }

    public function valid_time_format($str)
    {
        if (empty($str)) {
            return true;
        }
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $str)) {
            return true;
        }
        $this->form_validation->set_message('valid_time_format', lang('invalid_time_format'));
        return false;
    }

    public function delete($id = null)
    {
        $this->sma->checkPermissions('delete', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $appointment = $this->customer_appointment_model->getById($id);
        if (!$appointment) {
            $this->sma->send_json(array('error' => 1, 'msg' => lang('no_data_found')));
        }
        if ($this->customer_appointment_model->delete($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang('data_deleted')));
            }
            $this->session->set_flashdata('message', lang('data_deleted'));
        }
        admin_redirect('appointments');
    }
}