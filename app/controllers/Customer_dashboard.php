<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Public customer dashboard - access without login via base_url/customers/{code}
 * Code can be customer id (numeric) or customer_code (if companies.customer_code column exists).
 */
class Customer_dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('site');
        $this->load->model('customer_case_model');
        $this->load->model('customer_appointment_model');
        $this->Settings = $this->site->get_setting();
        $this->load->admin_model('reports_model');
    }

    /**
     * Display customer dashboard by code (id or customer_code)
     */
    public function view($code = NULL) {
       
        if (empty($code)) {
            show_404();
            return;
        }

        $customer = $this->site->getCompanyByCustomerCode($code);
        if (!$customer) {
            show_404();
            return;
        }

        $customer_id = $customer->id;
        $totals = $this->reports_model->getSalesTotals($customer_id);
        $sales_count = $this->reports_model->getCustomerSales($customer_id);
        $quotes_count = $this->reports_model->getCustomerQuotes($customer_id);
        $sales_list = $this->reports_model->getCustomerSalesList($customer_id, 15);
        $quotes_list = $this->reports_model->getCustomerQuotesList($customer_id, 10);

        // Fixed service gauges: Support, Security, Firewall — duration from last sale support_duration
        $last_sale_support = $this->reports_model->getCustomerLastSaleSupport($customer_id);
        $dashboard_services = array();
        $today = new DateTime('today');
        $service_names = array('Support', 'Security', 'Firewall');
        $base_data = array(
            'sale_id' => 0,
            'sale_date' => null,
            'support_duration' => 0,
            'end_date' => null,
            'remaining_days' => null,
            'percent_remaining' => null,
            'status_class' => 'no-expiry',
        );
        if ($last_sale_support) {
            $support_days = isset($last_sale_support->support_duration) ? (int)$last_sale_support->support_duration : 0;
            $sale_date = new DateTime($last_sale_support->date);
            $sale_date->setTime(0, 0, 0);
            $today->setTime(0, 0, 0);
            // Expiry = sale date + support_duration (days)
            $end_date = $support_days > 0 ? (clone $sale_date)->modify("+{$support_days} days") : null;
            $remaining_days = null;
            $percent_remaining = null;
            $status_class = 'no-expiry';
            if ($end_date) {
                $end_date->setTime(0, 0, 0);
                $today_str = $today->format('Y-m-d');
                $end_date_str = $end_date->format('Y-m-d');
                $support_passed = ($end_date_str < $today_str);
                if ($support_passed) {
                    $percent_remaining = 0;
                    $status_class = 'expired';
                    $remaining_days = (int) $today->diff($end_date)->days;
                    $remaining_days = -abs($remaining_days);
                } else {
                    $interval = $today->diff($end_date, true);
                    $remaining_days = (int) $interval->days;
                    $total_days = (int) $sale_date->diff($end_date)->days;
                    $percent_remaining = $total_days > 0 ? min(100, round(($remaining_days / $total_days) * 100)) : 100;
                    $status_class = $percent_remaining > 40 ? 'green' : ($percent_remaining > 15 ? 'yellow' : 'red');
                }
            }
            $base_data = array(
                'sale_id' => (int)$last_sale_support->id,
                'sale_date' => $last_sale_support->date,
                'support_duration' => $support_days,
                'end_date' => $end_date ? $end_date->format('Y-m-d') : null,
                'remaining_days' => $remaining_days,
                'percent_remaining' => $percent_remaining,
                'status_class' => $status_class,
            );
        }
        foreach ($service_names as $name) {
            $dashboard_services[] = (object)array_merge(array('service_name' => $name), $base_data);
        }

        // Header expiry: from last sale date + support_duration days (same as gauge)
        $support_expiry_date_formatted = null;
        if (!empty($dashboard_services) && !empty($dashboard_services[0]->end_date)) {
            $df = ($sd = $this->site->getDateFormat($this->Settings->dateformat)) ? $sd->php : 'd/m/Y';
            $support_expiry_date_formatted = date($df, strtotime($dashboard_services[0]->end_date));
        }

        // Sales & Technical Associate from most recent sale (for dashboard "Your team" section)
        $dashboard_products = array();
        $dashboard_products_raw = $this->reports_model->getCustomerDashboardProducts($customer_id, 1);
        foreach ($dashboard_products_raw as $row) {
            $sales_associate_name = '';
            $tech_associate_name = '';
            if (!empty($row->assign_marketing_officers)) {
                $u = $this->site->getUserById($row->assign_marketing_officers);
                if ($u) $sales_associate_name = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));
            }
            if (!empty($row->service_provider)) {
                $u = $this->site->getUserById($row->service_provider);
                if ($u) $tech_associate_name = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));
            }
            $dashboard_products[] = (object)array('sales_associate_name' => $sales_associate_name, 'tech_associate_name' => $tech_associate_name);
        }

        $total_amount = $totals ? (float)$totals->total_amount : 0;
        $paid_amount = $totals ? (float)$totals->paid : 0;
        $balance = $total_amount - $paid_amount;

        $customer_name = trim(($customer->company && $customer->company != '-') ? $customer->company : ($customer->name . ' ' . trim($customer->last_name ?? '')));
        $person_name = trim(($customer->name ?? '') . ' ' . trim($customer->last_name ?? ''));
        $company_name = ($customer->company && $customer->company != '-') ? trim($customer->company) : '';
        $header_secondary_name = '';
        if ($customer_name === $company_name && $person_name !== '') {
            $header_secondary_name = $person_name;
        } elseif ($customer_name !== $company_name && $company_name !== '') {
            $header_secondary_name = $company_name;
        }

        // Sales & Technical Associate from most recent sale (for dashboard "Your team" section)
        $customer_sales_associate_name = '';
        $customer_tech_associate_name = '';
        if (!empty($dashboard_products)) {
            $customer_sales_associate_name = $dashboard_products[0]->sales_associate_name ?? '';
            $customer_tech_associate_name = $dashboard_products[0]->tech_associate_name ?? '';
        }
        if ($customer_sales_associate_name === '' || $customer_tech_associate_name === '') {
            $latest_sale = $this->reports_model->getCustomerLatestSaleAssociates($customer_id);
            if ($latest_sale) {
                if (!empty($latest_sale->assign_marketing_officers)) {
                    $u = $this->site->getUserById($latest_sale->assign_marketing_officers);
                    if ($u) $customer_sales_associate_name = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));
                }
                if (!empty($latest_sale->service_provider)) {
                    $u = $this->site->getUserById($latest_sale->service_provider);
                    if ($u) $customer_tech_associate_name = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));
                }
            }
        }

        $this->data['customer'] = $customer;
        $this->data['customer_name'] = $customer_name;
        $this->data['header_secondary_name'] = $header_secondary_name;
        $this->data['customer_sales_associate_name'] = $customer_sales_associate_name;
        $this->data['customer_tech_associate_name'] = $customer_tech_associate_name;
        $this->data['dashboard_services'] = $dashboard_services;
        $this->data['support_expiry_date_formatted'] = $support_expiry_date_formatted;
        $this->data['dashboard_products'] = $dashboard_products;
        $this->data['sales_list'] = $sales_list;
        $this->data['quotes_list'] = $quotes_list;
        $this->data['sales_count'] = $sales_count;
        $this->data['quotes_count'] = $quotes_count;
        $this->data['total_amount'] = $total_amount;
        $this->data['paid_amount'] = $paid_amount;
        $this->data['balance'] = $balance;
        $this->data['Settings'] = $this->Settings;
        $this->data['site_name'] = $this->Settings->site_name;
        $this->data['logo_url'] = !empty($this->Settings->logo) ? base_url('assets/uploads/logos/' . $this->Settings->logo) : '';
        $this->data['currency'] = $this->site->getCurrencyByCode($this->Settings->default_currency);
        $this->data['date_format'] = ($sd = $this->site->getDateFormat($this->Settings->dateformat)) ? $sd->php : 'd/m/Y';

        // Cases (Open Case / Case History) — cannot open new case until current one is closed
        $this->data['case_list'] = $this->customer_case_model->getByCustomerId($customer_id);
        $this->data['customer_code'] = $code; // for AJAX submit URL
        $this->data['can_open_case'] = !$this->customer_case_model->hasOpenCase($customer_id);

        // Appointments (Book Appointment / Appointment History)
        $this->data['appointment_list'] = $this->customer_appointment_model->getByCustomerId($customer_id);
        $this->data['upcoming_appointments'] = $this->customer_appointment_model->getUpcomingByCustomerId($customer_id);
        $this->data['past_appointments'] = $this->customer_appointment_model->getPastByCustomerId($customer_id);
        $this->data['appointment_types'] = $this->customer_appointment_model->getAppointmentTypes();
        $this->data['duration_options'] = $this->customer_appointment_model->getDurationOptions();
        $this->data['can_book_appointment'] = !$this->customer_appointment_model->hasActiveAppointment($customer_id);

        // CSRF token for AJAX case submit
        $this->data['csrf_token_name'] = $this->security->get_csrf_token_name();
        $this->data['csrf_hash'] = $this->security->get_csrf_hash();

        $this->load->view('customer_dashboard/dashboard', $this->data);
    }

    /**
     * AJAX: Submit a new case. Expects POST details. Returns JSON.
     */
    public function submit_case($code = NULL) {
        header('Content-Type: application/json');
        if (empty($code) || !$this->input->is_ajax_request()) {
            echo json_encode(array('success' => false, 'message' => 'Invalid request.'));
            return;
        }
        $customer = $this->site->getCompanyByCustomerCode($code);
        if (!$customer) {
            echo json_encode(array('success' => false, 'message' => 'Customer not found.'));
            return;
        }
        if ($this->customer_case_model->hasOpenCase($customer->id)) {
            echo json_encode(array('success' => false, 'message' => 'You cannot open a new case until your current case is resolved (closed).'));
            return;
        }
        $details = $this->input->post('details');
        $customer_code = isset($customer->customer_code) ? $customer->customer_code : $code;
        $result = $this->customer_case_model->add($customer->id, $details, $customer_code);
        if (!empty($result['success'])) {
            $date_format = ($sd = $this->site->getDateFormat($this->Settings->dateformat)) ? $sd->php : 'd/m/Y';
            $result['date_formatted'] = date($date_format, strtotime($result['created_at']));
            $result['status'] = 'open';
            $email_result = $this->_sendCaseEmails($customer, $result['case_code'], $details, $result['created_at'], $date_format);
            $result['email_sent'] = $email_result;
        }
        echo json_encode($result);
    }

    /**
     * Send email to staff (a.kader@commodore.inc) and to customer when a case is created.
     * Returns array: staff_ok (bool), customer_ok (bool), staff_error (string), customer_error (string).
     */
    private function _sendCaseEmails($customer, $case_code, $details, $created_at, $date_format = 'd/m/Y') {
        $result = array('staff_ok' => false, 'customer_ok' => false, 'staff_error' => '', 'customer_error' => '', 'protocol_used' => '');
        $this->load->library('email');
        $protocol = isset($this->Settings->protocol) ? $this->Settings->protocol : 'mail';
        $result['protocol_used'] = $protocol;

        $config = array(
            'useragent' => $this->Settings->site_name,
            'protocol'  => $protocol,
            'mailtype'  => 'html',
            'crlf'      => "\r\n",
            'newline'   => "\r\n",
        );
        if (!empty($protocol) && $protocol == 'sendmail' && !empty($this->Settings->mailpath)) {
            $config['mailpath'] = $this->Settings->mailpath;
        }
        if (!empty($protocol) && $protocol == 'smtp') {
            $config['smtp_host'] = isset($this->Settings->smtp_host) ? $this->Settings->smtp_host : '';
            $config['smtp_user'] = isset($this->Settings->smtp_user) ? $this->Settings->smtp_user : '';
            $config['smtp_pass'] = isset($this->Settings->smtp_pass) ? $this->Settings->smtp_pass : '';
            $config['smtp_port'] = isset($this->Settings->smtp_port) ? $this->Settings->smtp_port : 25;
            if (!empty($this->Settings->smtp_crypto)) {
                $config['smtp_crypto'] = $this->Settings->smtp_crypto;
            }
        }
        $this->email->initialize($config);

        // Many cPanel/SMTP servers only deliver when From = authenticated account (smtp_user)
        if (!empty($protocol) && $protocol == 'smtp' && !empty($this->Settings->smtp_user)) {
            $from_email = $this->Settings->smtp_user;
        } else {
            $from_email = !empty($this->Settings->default_email) ? $this->Settings->default_email : 'noreply@' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
        }
        $from_name  = !empty($this->Settings->site_name) ? $this->Settings->site_name : 'CRM';
        $date_fmt   = date($date_format, strtotime($created_at));
        $customer_name = trim(($customer->name ?? '') . ' ' . ($customer->last_name ?? ''));
        if ($customer_name === '') {
            $customer_name = $customer->company ?? 'Customer';
        }
        $customer_email = isset($customer->email) ? trim($customer->email) : '';

        // 1. Email to staff: a.kader@commodore.inc (professional HTML)
        $staff_to = 'a.kader@commodore.inc';
        $staff_subject = 'New case created: ' . $case_code . ' - ' . $this->Settings->site_name;
        $staff_body = $this->_caseEmailTemplate('staff', array(
            'site_name'      => $this->Settings->site_name,
            'case_code'      => $case_code,
            'date_fmt'       => $date_fmt,
            'customer_name'  => $customer_name,
            'customer_email' => $customer_email ?: '—',
            'details'        => $details,
        ));
        $this->email->from($from_email, $from_name);
        $this->email->to($staff_to);
        $this->email->subject($staff_subject);
        $this->email->message($staff_body);
        if ($this->email->send()) {
            $result['staff_ok'] = true;
        } else {
            $result['staff_error'] = $this->email->print_debugger(array('headers', 'subject'), true);
        }
        $this->email->clear();

        // 2. Email to customer (confirmation) – professional HTML
        if ($customer_email !== '' && filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
            $cust_subject = 'Case registered: ' . $case_code . ' - ' . $this->Settings->site_name;
            $cust_body = $this->_caseEmailTemplate('customer', array(
                'site_name'     => $this->Settings->site_name,
                'case_code'     => $case_code,
                'date_fmt'      => $date_fmt,
                'customer_name' => $customer_name,
                'details'       => $details,
            ));
            $this->email->from($from_email, $from_name);
            $this->email->to($customer_email);
            $this->email->subject($cust_subject);
            $this->email->message($cust_body);
            if ($this->email->send()) {
                $result['customer_ok'] = true;
            } else {
                $result['customer_error'] = $this->email->print_debugger(array('headers', 'subject'), true);
            }
        }
        return $result;
    }

    /**
     * Professional HTML email template for case notifications (inline styles for email clients).
     * @param string $type 'staff' or 'customer'
     * @param array $data site_name, case_code, date_fmt, customer_name, details; staff also has customer_email
     */
    private function _caseEmailTemplate($type, $data) {
        $site_name = isset($data['site_name']) ? $data['site_name'] : 'Support';
        $case_code = isset($data['case_code']) ? htmlspecialchars($data['case_code']) : '';
        $date_fmt  = isset($data['date_fmt']) ? htmlspecialchars($data['date_fmt']) : '';
        $customer_name = isset($data['customer_name']) ? htmlspecialchars($data['customer_name']) : '';
        $details   = isset($data['details']) ? nl2br(htmlspecialchars($data['details'])) : '';
        $customer_email = isset($data['customer_email']) ? htmlspecialchars($data['customer_email']) : '—';

        $primary   = '#4f46e5';
        $primary_dark = '#4338ca';
        $bg_light  = '#f8fafc';
        $border    = '#e2e8f0';
        $text      = '#1e293b';
        $text_muted = '#64748b';
        $success   = '#059669';

        $header_title = ($type === 'staff') ? 'New Support Case' : 'Case Registered';
        $header_sub   = ($type === 'staff') ? 'A new case has been submitted' : 'Your case has been received';

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head><body style="margin:0; padding:0; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; font-size: 15px; line-height: 1.6; color: ' . $text . '; background-color: #f1f5f9;">';
        $html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 24px 16px;"><tr><td align="center">';
        $html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 560px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">';

        // Header
        $html .= '<tr><td style="background: linear-gradient(135deg, ' . $primary . ' 0%, ' . $primary_dark . ' 100%); padding: 24px 28px; text-align: center;">';
        $html .= '<div style="color: #ffffff; font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px;">' . htmlspecialchars($site_name) . '</div>';
        $html .= '<h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 600;">' . $header_title . '</h1>';
        $html .= '<p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">' . $header_sub . '</p>';
        $html .= '</td></tr>';

        // Case code badge
        $html .= '<tr><td style="padding: 24px 28px 0 28px; text-align: center;">';
        $html .= '<span style="display: inline-block; background-color: ' . $primary . '; color: #ffffff; font-size: 14px; font-weight: 600; padding: 10px 20px; border-radius: 8px; letter-spacing: 0.02em;">' . $case_code . '</span>';
        $html .= '</td></tr>';

        // Content block
        $html .= '<tr><td style="padding: 24px 28px;">';

        if ($type === 'staff') {
            $html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">';
            $html .= '<tr><td style="padding: 12px 16px; background-color: ' . $bg_light . '; border-radius: 8px; border-left: 4px solid ' . $primary . ';"><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase;">Date</span><br><span style="font-weight: 600; color: ' . $text . ';">' . $date_fmt . '</span></td></tr>';
            $html .= '<tr><td style="height: 8px;"></td></tr>';
            $html .= '<tr><td style="padding: 12px 16px; background-color: ' . $bg_light . '; border-radius: 8px; border-left: 4px solid ' . $primary . ';"><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase;">Customer</span><br><span style="font-weight: 600; color: ' . $text . ';">' . $customer_name . '</span></td></tr>';
            $html .= '<tr><td style="height: 8px;"></td></tr>';
            $html .= '<tr><td style="padding: 12px 16px; background-color: ' . $bg_light . '; border-radius: 8px; border-left: 4px solid ' . $primary . ';"><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase;">Customer email</span><br><span style="font-weight: 600; color: ' . $text . ';">' . $customer_email . '</span></td></tr>';
            $html .= '</table>';
        } else {
            $html .= '<p style="margin: 0 0 16px 0; color: ' . $text . ';">Hello ' . $customer_name . ',</p>';
            $html .= '<p style="margin: 0 0 20px 0; color: ' . $text_muted . ';">Your support case has been registered. We will get back to you soon.</p>';
            $html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">';
            $html .= '<tr><td style="padding: 12px 16px; background-color: ' . $bg_light . '; border-radius: 8px; border-left: 4px solid ' . $primary . ';"><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase;">Date</span><br><span style="font-weight: 600; color: ' . $text . ';">' . $date_fmt . '</span></td></tr>';
            $html .= '</table>';
        }

        $html .= '<div style="margin-bottom: 8px; color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase; font-weight: 600;">Details</div>';
        $html .= '<div style="padding: 16px; background-color: ' . $bg_light . '; border: 1px solid ' . $border . '; border-radius: 8px; color: ' . $text . ';">' . $details . '</div>';

        $html .= '</td></tr>';

        // Footer
        $html .= '<tr><td style="padding: 20px 28px 24px 28px; border-top: 1px solid ' . $border . '; text-align: center; color: ' . $text_muted . '; font-size: 12px;">';
        $html .= 'This is an automated message from ' . htmlspecialchars($site_name) . '.';
        $html .= '</td></tr>';
        $html .= '</table></td></tr></table></body></html>';

        return $html;
    }

    /**
     * AJAX: Submit a new appointment. Expects POST data. Returns JSON.
     */
    public function submit_appointment($code = NULL) {
        header('Content-Type: application/json');
        if (empty($code) || !$this->input->is_ajax_request()) {
            echo json_encode(array('success' => false, 'message' => 'Invalid request.'));
            return;
        }
        $customer = $this->site->getCompanyByCustomerCode($code);
        if (!$customer) {
            echo json_encode(array('success' => false, 'message' => 'Customer not found.'));
            return;
        }

        // Check if customer already has an active appointment
        if ($this->customer_appointment_model->hasActiveAppointment($customer->id)) {
            echo json_encode(array('success' => false, 'message' => 'You cannot book a new appointment until your current appointment is completed or the date has passed.'));
            return;
        }

        // Get POST data
        $data = array(
            'appointment_type' => $this->input->post('appointment_type'),
            'subject'          => $this->input->post('subject'),
            'description'      => $this->input->post('description'),
            'preferred_date'   => $this->input->post('preferred_date'),
            'preferred_time'   => $this->input->post('preferred_time'),
            'duration_minutes' => $this->input->post('duration_minutes'),
        );

        $customer_code = isset($customer->customer_code) ? $customer->customer_code : $code;
        $result = $this->customer_appointment_model->add($customer->id, $data, $customer_code);
        
        if (!empty($result['success'])) {
            $date_format = ($sd = $this->site->getDateFormat($this->Settings->dateformat)) ? $sd->php : 'd/m/Y';
            $result['date_formatted'] = date($date_format, strtotime($result['created_at']));
            $result['preferred_date_formatted'] = date($date_format, strtotime($data['preferred_date']));
            $result['status'] = 'pending';
            
            // Send email notifications
            $email_result = $this->_sendAppointmentEmails($customer, $result['appointment_code'], $data, $result['created_at'], $date_format);
            $result['email_sent'] = $email_result;
        }
        echo json_encode($result);
    }

    /**
     * Send email to staff (a.kader@commodore.inc) and to customer when an appointment is created.
     * Returns array: staff_ok (bool), customer_ok (bool), staff_error (string), customer_error (string).
     */
    private function _sendAppointmentEmails($customer, $appointment_code, $data, $created_at, $date_format = 'd/m/Y') {
        $result = array('staff_ok' => false, 'customer_ok' => false, 'staff_error' => '', 'customer_error' => '', 'protocol_used' => '');
        $this->load->library('email');
        $protocol = isset($this->Settings->protocol) ? $this->Settings->protocol : 'mail';
        $result['protocol_used'] = $protocol;

        $config = array(
            'useragent' => $this->Settings->site_name,
            'protocol'  => $protocol,
            'mailtype'  => 'html',
            'crlf'      => "\r\n",
            'newline'   => "\r\n",
        );
        if (!empty($protocol) && $protocol == 'sendmail' && !empty($this->Settings->mailpath)) {
            $config['mailpath'] = $this->Settings->mailpath;
        }
        if (!empty($protocol) && $protocol == 'smtp') {
            $config['smtp_host'] = isset($this->Settings->smtp_host) ? $this->Settings->smtp_host : '';
            $config['smtp_user'] = isset($this->Settings->smtp_user) ? $this->Settings->smtp_user : '';
            $config['smtp_pass'] = isset($this->Settings->smtp_pass) ? $this->Settings->smtp_pass : '';
            $config['smtp_port'] = isset($this->Settings->smtp_port) ? $this->Settings->smtp_port : 25;
            if (!empty($this->Settings->smtp_crypto)) {
                $config['smtp_crypto'] = $this->Settings->smtp_crypto;
            }
        }
        $this->email->initialize($config);

        // Many cPanel/SMTP servers only deliver when From = authenticated account (smtp_user)
        if (!empty($protocol) && $protocol == 'smtp' && !empty($this->Settings->smtp_user)) {
            $from_email = $this->Settings->smtp_user;
        } else {
            $from_email = !empty($this->Settings->default_email) ? $this->Settings->default_email : 'noreply@' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
        }
        $from_name  = !empty($this->Settings->site_name) ? $this->Settings->site_name : 'CRM';
        $date_fmt   = date($date_format, strtotime($created_at));
        $customer_name = trim(($customer->name ?? '') . ' ' . ($customer->last_name ?? ''));
        if ($customer_name === '') {
            $customer_name = $customer->company ?? 'Customer';
        }
        $customer_email = isset($customer->email) ? trim($customer->email) : '';

        // Format appointment details
        $appointment_type_label = isset($this->customer_appointment_model->getAppointmentTypes()[$data['appointment_type']]) 
            ? $this->customer_appointment_model->getAppointmentTypes()[$data['appointment_type']] 
            : $data['appointment_type'];
        
        $duration_label = isset($this->customer_appointment_model->getDurationOptions()[$data['duration_minutes']]) 
            ? $this->customer_appointment_model->getDurationOptions()[$data['duration_minutes']] 
            : $data['duration_minutes'] . ' minutes';

        $preferred_date_fmt = date($date_format, strtotime($data['preferred_date']));
        $preferred_time_fmt = date('h:i A', strtotime($data['preferred_time']));

        // 1. Email to staff: a.kader@commodore.inc
        $staff_to = 'a.kader@commodore.inc';
        $staff_subject = 'New appointment request: ' . $appointment_code . ' - ' . $this->Settings->site_name;
        $staff_body = $this->_appointmentEmailTemplate('staff', array(
            'site_name'         => $this->Settings->site_name,
            'appointment_code'  => $appointment_code,
            'date_fmt'          => $date_fmt,
            'customer_name'     => $customer_name,
            'customer_email'    => $customer_email ?: '—',
            'appointment_type'  => $appointment_type_label,
            'subject'           => $data['subject'],
            'description'       => $data['description'] ?? '',
            'preferred_date'    => $preferred_date_fmt,
            'preferred_time'    => $preferred_time_fmt,
            'duration'          => $duration_label,
        ));
        $this->email->from($from_email, $from_name);
        $this->email->to($staff_to);
        $this->email->subject($staff_subject);
        $this->email->message($staff_body);
        if ($this->email->send()) {
            $result['staff_ok'] = true;
        } else {
            $result['staff_error'] = $this->email->print_debugger(array('headers', 'subject'), true);
        }
        $this->email->clear();

        // 2. Email to customer (confirmation)
        if ($customer_email !== '' && filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
            $cust_subject = 'Appointment request received: ' . $appointment_code . ' - ' . $this->Settings->site_name;
            $cust_body = $this->_appointmentEmailTemplate('customer', array(
                'site_name'         => $this->Settings->site_name,
                'appointment_code'  => $appointment_code,
                'date_fmt'          => $date_fmt,
                'customer_name'     => $customer_name,
                'appointment_type'  => $appointment_type_label,
                'subject'           => $data['subject'],
                'description'       => $data['description'] ?? '',
                'preferred_date'    => $preferred_date_fmt,
                'preferred_time'    => $preferred_time_fmt,
                'duration'          => $duration_label,
            ));
            $this->email->from($from_email, $from_name);
            $this->email->to($customer_email);
            $this->email->subject($cust_subject);
            $this->email->message($cust_body);
            if ($this->email->send()) {
                $result['customer_ok'] = true;
            } else {
                $result['customer_error'] = $this->email->print_debugger(array('headers', 'subject'), true);
            }
        }
        return $result;
    }

    /**
     * Professional HTML email template for appointment notifications.
     * @param string $type 'staff' or 'customer'
     * @param array $data appointment details
     */
    private function _appointmentEmailTemplate($type, $data) {
        $site_name = isset($data['site_name']) ? $data['site_name'] : 'Support';
        $appointment_code = isset($data['appointment_code']) ? htmlspecialchars($data['appointment_code']) : '';
        $date_fmt  = isset($data['date_fmt']) ? htmlspecialchars($data['date_fmt']) : '';
        $customer_name = isset($data['customer_name']) ? htmlspecialchars($data['customer_name']) : '';
        $customer_email = isset($data['customer_email']) ? htmlspecialchars($data['customer_email']) : '—';
        $appointment_type = isset($data['appointment_type']) ? htmlspecialchars($data['appointment_type']) : '';
        $subject = isset($data['subject']) ? htmlspecialchars($data['subject']) : '';
        $description = isset($data['description']) ? nl2br(htmlspecialchars($data['description'])) : '';
        $preferred_date = isset($data['preferred_date']) ? htmlspecialchars($data['preferred_date']) : '';
        $preferred_time = isset($data['preferred_time']) ? htmlspecialchars($data['preferred_time']) : '';
        $duration = isset($data['duration']) ? htmlspecialchars($data['duration']) : '';

        $primary   = '#6366f1';
        $primary_dark = '#4f46e5';
        $bg_light  = '#f8fafc';
        $border    = '#e2e8f0';
        $text      = '#1e293b';
        $text_muted = '#64748b';
        $accent    = '#8b5cf6';

        $header_title = ($type === 'staff') ? 'New Appointment Request' : 'Appointment Request Received';
        $header_sub   = ($type === 'staff') ? 'A new appointment has been requested' : 'Your appointment request has been received';

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head><body style="margin:0; padding:0; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif; font-size: 15px; line-height: 1.6; color: ' . $text . '; background-color: #f1f5f9;">';
        $html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 24px 16px;"><tr><td align="center">';
        $html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 560px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">';

        // Header
        $html .= '<tr><td style="background: linear-gradient(135deg, ' . $primary . ' 0%, ' . $accent . ' 100%); padding: 24px 28px; text-align: center;">';
        $html .= '<div style="color: #ffffff; font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px;">' . htmlspecialchars($site_name) . '</div>';
        $html .= '<h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 600;">' . $header_title . '</h1>';
        $html .= '<p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">' . $header_sub . '</p>';
        $html .= '</td></tr>';

        // Appointment code badge
        $html .= '<tr><td style="padding: 24px 28px 0 28px; text-align: center;">';
        $html .= '<span style="display: inline-block; background-color: ' . $primary . '; color: #ffffff; font-size: 14px; font-weight: 600; padding: 10px 20px; border-radius: 8px; letter-spacing: 0.02em;">' . $appointment_code . '</span>';
        $html .= '</td></tr>';

        // Content block
        $html .= '<tr><td style="padding: 24px 28px;">';

        if ($type === 'staff') {
            $html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">';
            $html .= '<tr><td style="padding: 12px 16px; background-color: ' . $bg_light . '; border-radius: 8px; border-left: 4px solid ' . $primary . ';"><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase;">Request Date</span><br><span style="font-weight: 600; color: ' . $text . ';">' . $date_fmt . '</span></td></tr>';
            $html .= '<tr><td style="height: 8px;"></td></tr>';
            $html .= '<tr><td style="padding: 12px 16px; background-color: ' . $bg_light . '; border-radius: 8px; border-left: 4px solid ' . $primary . ';"><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase;">Customer</span><br><span style="font-weight: 600; color: ' . $text . ';">' . $customer_name . '</span></td></tr>';
            $html .= '<tr><td style="height: 8px;"></td></tr>';
            $html .= '<tr><td style="padding: 12px 16px; background-color: ' . $bg_light . '; border-radius: 8px; border-left: 4px solid ' . $primary . ';"><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase;">Customer Email</span><br><span style="font-weight: 600; color: ' . $text . ';">' . $customer_email . '</span></td></tr>';
            $html .= '</table>';
        } else {
            $html .= '<p style="margin: 0 0 16px 0; color: ' . $text . ';">Hello ' . $customer_name . ',</p>';
            $html .= '<p style="margin: 0 0 20px 0; color: ' . $text_muted . ';">Thank you for requesting an appointment. We will review your request and confirm the date and time shortly.</p>';
        }

        // Appointment details
        $html .= '<div style="background-color: ' . $bg_light . '; border-radius: 10px; padding: 20px; margin-bottom: 20px;">';
        $html .= '<div style="margin-bottom: 16px;"><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase; font-weight: 600;">Type</span><br><span style="font-weight: 600; color: ' . $text . '; font-size: 16px;">' . $appointment_type . '</span></div>';
        $html .= '<div style="margin-bottom: 16px;"><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase; font-weight: 600;">Subject</span><br><span style="font-weight: 600; color: ' . $text . ';">' . $subject . '</span></div>';
        $html .= '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">';
        $html .= '<div><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase; font-weight: 600;">Preferred Date</span><br><span style="font-weight: 600; color: ' . $primary . ';">' . $preferred_date . '</span></div>';
        $html .= '<div><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase; font-weight: 600;">Preferred Time</span><br><span style="font-weight: 600; color: ' . $primary . ';">' . $preferred_time . '</span></div>';
        $html .= '</div>';
        $html .= '<div><span style="color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase; font-weight: 600;">Duration</span><br><span style="font-weight: 600; color: ' . $text . ';">' . $duration . '</span></div>';
        $html .= '</div>';

        if ($description) {
            $html .= '<div style="margin-bottom: 8px; color: ' . $text_muted . '; font-size: 12px; text-transform: uppercase; font-weight: 600;">Description</div>';
            $html .= '<div style="padding: 16px; background-color: ' . $bg_light . '; border: 1px solid ' . $border . '; border-radius: 8px; color: ' . $text . ';">' . $description . '</div>';
        }

        $html .= '</td></tr>';

        // Footer
        $html .= '<tr><td style="padding: 20px 28px 24px 28px; border-top: 1px solid ' . $border . '; text-align: center; color: ' . $text_muted . '; font-size: 12px;">';
        if ($type === 'customer') {
            $html .= 'You will receive a confirmation email once your appointment is confirmed.<br>';
        }
        $html .= 'This is an automated message from ' . htmlspecialchars($site_name) . '.';
        $html .= '</td></tr>';
        $html .= '</table></td></tr></table></body></html>';

        return $html;
    }
}