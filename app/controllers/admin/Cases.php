<?php defined('BASEPATH') or exit('No direct script access allowed');

class Cases extends MY_Controller
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
        $this->load->model('customer_case_model');
    }

    public function index()
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('cases')));
        $meta = array('page_title' => lang('cases'), 'bc' => $bc);
        $this->page_construct('cases/index', $meta, $this->data);
    }

    public function getCases()
    {
        $this->sma->checkPermissions('index');

        $edit_link = anchor('admin/cases/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit'), 'class="sledit"');
        $delete_link = "<a href='#' class='po' title='<b>" . lang("Delete") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('cases/delete/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete') . "</a>";

        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">';
        if ($this->sma->actionPermissions('edit', 'cases')) {
            $action .= '<li>' . $edit_link . '</li>';
        }
        if ($this->sma->actionPermissions('delete', 'cases')) {
            $action .= '<li>' . $delete_link . '</li>';
        }
        $action .= '</ul></div></div>';

        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('customer_cases') . ".id as id, case_code, details, status, created_at, " . $this->db->dbprefix('companies') . ".name as customer_name")
            ->from('customer_cases')
            ->join('companies', 'companies.id = customer_cases.customer_id', 'left')
            ->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }

    public function add()
    {
        $this->sma->checkPermissions('add', true);

        $this->form_validation->set_rules('customer_id', lang("customer"), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('details', lang("details"), 'required');

        if ($this->form_validation->run() == true) {
            $customer_id = (int) $this->input->post('customer_id');
            $customer = $this->site->getCompanyByID($customer_id);
            $customer_code = $customer ? $customer->cf1 : null;
            $details = $this->input->post('details');
            $result = $this->customer_case_model->add($customer_id, $details, $customer_code);
            if (!empty($result['success'])) {
                $this->session->set_flashdata('message', lang('data_added'));
                admin_redirect('cases');
            } else {
                $this->session->set_flashdata('error', $result['message']);
            }
        }

        $this->data['customers'] = $this->site->getAllCompanies('customer');
        $this->data['status_options'] = $this->customer_case_model->getStatusOptions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('cases'), 'page' => lang('cases')), array('link' => '#', 'page' => lang('add_case')));
        $meta = array('page_title' => lang('add_case'), 'bc' => $bc);
        $this->page_construct('cases/add', $meta, $this->data);
    }

    public function edit($id = null)
    {
        $this->sma->checkPermissions('edit', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $case = $this->customer_case_model->getById($id);
        if (!$case) {
            $this->session->set_flashdata('error', lang('no_data_found'));
            admin_redirect('cases');
        }

        $this->form_validation->set_rules('details', lang("details"), 'required');
        $this->form_validation->set_rules('status', lang("status"), 'required');

        if ($this->form_validation->run() == true) {
            $data = array(
                'details' => $this->input->post('details'),
                'status'  => $this->input->post('status'),
            );
            if ($this->customer_case_model->update($id, $data)) {
                $this->session->set_flashdata('message', lang('data_updated'));
                admin_redirect('cases');
            }
        }

        $this->data['case'] = $case;
        $this->data['customer'] = $this->site->getCompanyByID($case->customer_id);
        $this->data['status_options'] = $this->customer_case_model->getStatusOptions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('cases'), 'page' => lang('cases')), array('link' => '#', 'page' => lang('edit')));
        $meta = array('page_title' => lang('edit'), 'bc' => $bc);
        $this->page_construct('cases/edit', $meta, $this->data);
    }

    public function delete($id = null)
    {
        $this->sma->checkPermissions('delete', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $case = $this->customer_case_model->getById($id);
        if (!$case) {
            $this->sma->send_json(array('error' => 1, 'msg' => lang('no_data_found')));
        }
        if ($this->customer_case_model->delete($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang('data_deleted')));
            }
            $this->session->set_flashdata('message', lang('data_deleted'));
        }
        admin_redirect('cases');
    }
}