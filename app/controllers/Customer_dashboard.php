<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Public customer dashboard - access without login via base_url/customers/{code}
 * Code can be customer id (numeric) or customer_code (if companies.customer_code column exists).
 */
class Customer_dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('site');
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

        // Featured products (order by sale id desc); remaining days = (sale_date + support_duration) to current date
        $dashboard_products_raw = $this->reports_model->getCustomerDashboardProducts($customer_id, 3);
        $dashboard_products = array();
        $today = new DateTime('today');
        foreach ($dashboard_products_raw as $row) {
            $support_days = isset($row->support_duration) ? (int)$row->support_duration : 0;
            $sale_date = new DateTime($row->sale_date);
            $end_date = $support_days > 0 ? (clone $sale_date)->modify("+{$support_days} days") : null;
            $remaining_days = null; // days from today to end_date (negative if expired)
            $percent_remaining = null;
            $status_class = 'no-expiry';
            if ($end_date) {
                $interval = $today->diff($end_date);
                $remaining_days = ($end_date < $today) ? -$interval->days : $interval->days;
                if ($end_date < $today) {
                    $percent_remaining = 0;
                    $status_class = 'expired';
                } else {
                    $total_days = $sale_date->diff($end_date)->days;
                    $percent_remaining = $total_days > 0 ? min(100, round(($remaining_days / $total_days) * 100)) : 100;
                    $status_class = $percent_remaining > 30 ? 'green' : ($percent_remaining > 10 ? 'yellow' : 'red');
                }
            }
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
            $dashboard_products[] = (object)array(
                'product_name' => $row->product_name,
                'product_code' => $row->product_code,
                'sale_id' => $row->sale_id,
                'reference_no' => $row->reference_no,
                'sale_date' => $row->sale_date,
                'support_duration' => $support_days,
                'end_date' => $end_date ? $end_date->format('Y-m-d') : null,
                'remaining_days' => $remaining_days,
                'percent_remaining' => $percent_remaining,
                'status_class' => $status_class,
                'sales_associate_name' => $sales_associate_name,
                'tech_associate_name' => $tech_associate_name,
            );
        }

        $total_amount = $totals ? (float)$totals->total_amount : 0;
        $paid_amount = $totals ? (float)$totals->paid : 0;
        $balance = $total_amount - $paid_amount;

        $customer_name = trim(($customer->company && $customer->company != '-') ? $customer->company : ($customer->name . ' ' . trim($customer->last_name ?? '')));

        // Sales & Technical Associate from most recent sale (for dashboard "Your team" section)
        $customer_sales_associate_name = '';
        $customer_tech_associate_name = '';
        if (!empty($dashboard_products)) {
            $customer_sales_associate_name = $dashboard_products[0]->sales_associate_name ?? '';
            $customer_tech_associate_name = $dashboard_products[0]->tech_associate_name ?? '';
        } else {
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
        $this->data['customer_sales_associate_name'] = $customer_sales_associate_name;
        $this->data['customer_tech_associate_name'] = $customer_tech_associate_name;
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
        $this->data['currency'] = $this->site->getCurrencyByCode($this->Settings->default_currency);
        $this->data['date_format'] = ($sd = $this->site->getDateFormat($this->Settings->dateformat)) ? $sd->php : 'd/m/Y';

        $this->load->view('customer_dashboard/dashboard', $this->data);
    }
}