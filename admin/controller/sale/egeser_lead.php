<?php
class ControllerSaleEgeserLead extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('sale/egeser_lead');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('sale/egeser_lead');

        $this->getList();
    }

    public function view() {
        $this->load->language('sale/egeser_lead');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('sale/egeser_lead');

        $lead_id = isset($this->request->get['lead_id']) ? (int)$this->request->get['lead_id'] : 0;
        $lead = $this->model_sale_egeser_lead->getLead($lead_id);

        if (!$lead) {
            $this->session->data['warning'] = $this->language->get('error_not_found');
            $this->response->redirect($this->url->link('sale/egeser_lead', 'token=' . $this->session->data['token'], true));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateModify()) {
            $this->model_sale_egeser_lead->updateLead($lead_id, array(
                'lead_status' => isset($this->request->post['lead_status']) ? $this->request->post['lead_status'] : 'Yeni',
                'assigned_to' => isset($this->request->post['assigned_to']) ? $this->request->post['assigned_to'] : '',
                'admin_note' => isset($this->request->post['admin_note']) ? $this->request->post['admin_note'] : ''
            ));
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('sale/egeser_lead/view', 'token=' . $this->session->data['token'] . '&lead_id=' . $lead_id, true));
        }

        $lead = $this->model_sale_egeser_lead->getLead($lead_id);

        $data['heading_title'] = $this->language->get('heading_title');
        $data['lead'] = $lead;
        $data['statuses'] = array('Yeni','Arandı','Teklif Verildi','Satış','İptal');
        $data['action'] = $this->url->link('sale/egeser_lead/view', 'token=' . $this->session->data['token'] . '&lead_id=' . $lead_id, true);
        $data['cancel'] = $this->url->link('sale/egeser_lead', 'token=' . $this->session->data['token'], true);
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
        unset($this->session->data['success']);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sale/egeser_lead_view', $data));
    }

    public function delete() {
        $this->load->language('sale/egeser_lead');
        $this->load->model('sale/egeser_lead');

        if (!$this->validateModify()) {
            $this->session->data['warning'] = $this->language->get('error_permission');
            $this->response->redirect($this->url->link('sale/egeser_lead', 'token=' . $this->session->data['token'], true));
        }

        $lead_id = isset($this->request->get['lead_id']) ? (int)$this->request->get['lead_id'] : 0;
        if ($lead_id) $this->model_sale_egeser_lead->deleteLead($lead_id);

        $this->session->data['success'] = $this->language->get('text_deleted');
        $this->response->redirect($this->url->link('sale/egeser_lead', 'token=' . $this->session->data['token'], true));
    }

    public function export() {
        $this->load->model('sale/egeser_lead');

        $filters = $this->getFilters();
        $filters['start'] = 0;
        $filters['limit'] = 5000;
        $rows = $this->model_sale_egeser_lead->getLeads($filters);

        $filename = 'teklif-talepleri-' . date('Y-m-d-His') . '.csv';
        $this->response->addHeader('Content-Type: text/csv; charset=UTF-8');
        $this->response->addHeader('Content-Disposition: attachment; filename="' . $filename . '"');

        $fp = fopen('php://temp', 'r+');
        fwrite($fp, "\xEF\xBB\xBF");
        fputcsv($fp, array('ID','Tarih','Durum','Müşteri Tipi','Firma','Ad Soyad','Telefon','E-posta','Yapı Türü','Lokasyon','m²','Ürün','Mesaj','Kaynak','UTM Source','UTM Medium','UTM Campaign','Mail Durumu','Atanan','Not'), ';');

        foreach ($rows as $r) {
            fputcsv($fp, array(
                $r['lead_id'],$r['date_added'],$r['lead_status'],$r['customer_type'],$r['company'],$r['name'],$r['phone'],$r['email'],
                $r['project_type'],$r['location'],$r['area'],$r['product_name'],$r['message'],$r['source'],$r['utm_source'],$r['utm_medium'],
                $r['utm_campaign'],$r['mail_status'],$r['assigned_to'],$r['admin_note']
            ), ';');
        }

        rewind($fp);
        $output = stream_get_contents($fp);
        fclose($fp);
        $this->response->setOutput($output);
    }

    private function getList() {
        $data['heading_title'] = $this->language->get('heading_title');
        // EGESER: Template icinde $this->session kullanilmaz. Token controller tarafindan aktarilir.
        $data['token'] = isset($this->session->data['token']) ? $this->session->data['token'] : '';

        $filters = $this->getFilters();
        $page = isset($this->request->get['page']) ? max(1, (int)$this->request->get['page']) : 1;
        $limit = 25;

        $query_data = $filters;
        $query_data['start'] = ($page - 1) * $limit;
        $query_data['limit'] = $limit;

        $total = $this->model_sale_egeser_lead->getTotalLeads($filters);
        $results = $this->model_sale_egeser_lead->getLeads($query_data);

        $data['leads'] = array();
        foreach ($results as $result) {
            $result['view'] = $this->url->link('sale/egeser_lead/view', 'token=' . $this->session->data['token'] . '&lead_id=' . (int)$result['lead_id'], true);
            $result['delete'] = $this->url->link('sale/egeser_lead/delete', 'token=' . $this->session->data['token'] . '&lead_id=' . (int)$result['lead_id'], true);
            $data['leads'][] = $result;
        }

        foreach ($filters as $key => $value) $data[$key] = $value;
        $data['statuses'] = array('Yeni','Arandı','Teklif Verildi','Satış','İptal');

        $url = $this->buildUrl($filters);
        $data['filter_action'] = $this->url->link('sale/egeser_lead', 'token=' . $this->session->data['token'], true);
        $data['export'] = $this->url->link('sale/egeser_lead/export', 'token=' . $this->session->data['token'] . $url, true);
        $data['reset'] = $this->url->link('sale/egeser_lead', 'token=' . $this->session->data['token'], true);

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('sale/egeser_lead', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);
        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit), $total, ceil($total / $limit));

        $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
        $data['warning'] = isset($this->session->data['warning']) ? $this->session->data['warning'] : '';
        unset($this->session->data['success'], $this->session->data['warning']);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sale/egeser_lead_list', $data));
    }

    private function getFilters() {
        return array(
            'filter_name' => isset($this->request->get['filter_name']) ? trim($this->request->get['filter_name']) : '',
            'filter_phone' => isset($this->request->get['filter_phone']) ? trim($this->request->get['filter_phone']) : '',
            'filter_location' => isset($this->request->get['filter_location']) ? trim($this->request->get['filter_location']) : '',
            'filter_status' => isset($this->request->get['filter_status']) ? trim($this->request->get['filter_status']) : '',
            'filter_customer_type' => isset($this->request->get['filter_customer_type']) ? trim($this->request->get['filter_customer_type']) : '',
            'filter_project_type' => isset($this->request->get['filter_project_type']) ? trim($this->request->get['filter_project_type']) : '',
            'filter_date_from' => isset($this->request->get['filter_date_from']) ? trim($this->request->get['filter_date_from']) : '',
            'filter_date_to' => isset($this->request->get['filter_date_to']) ? trim($this->request->get['filter_date_to']) : '',
            'sort' => isset($this->request->get['sort']) ? $this->request->get['sort'] : 'lead_id',
            'order' => isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC'
        );
    }

    private function buildUrl($filters) {
        $url = '';
        foreach ($filters as $key => $value) {
            if ($value !== '') $url .= '&' . $key . '=' . urlencode($value);
        }
        return $url;
    }

    protected function validateModify() {
        if (!$this->user->hasPermission('modify', 'sale/egeser_lead')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
}
