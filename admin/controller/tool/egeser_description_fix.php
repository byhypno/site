<?php
class ControllerToolEgeserDescriptionFix extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('tool/egeser_description_fix');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tool/egeser_description_fix');

        $data = array();
        $data['success'] = '';
        $data['error_warning'] = '';

        if (isset($this->session->data['egeser_descfix_success'])) {
            $data['success'] = $this->session->data['egeser_descfix_success'];
            unset($this->session->data['egeser_descfix_success']);
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && !empty($this->request->post['confirm_apply'])) {
            if (!$this->validatePermission()) {
                $data['error_warning'] = $this->error['warning'];
            } else {
                $product_ids = isset($this->request->post['product_id']) ? (array)$this->request->post['product_id'] : array();
                $updated = $this->model_tool_egeser_description_fix->applyFix($product_ids);
                $this->session->data['egeser_descfix_success'] = $updated . ' ürünün açıklaması düzeltildi.';
                $this->response->redirect($this->url->link('tool/egeser_description_fix', 'token=' . $this->session->data['token'], true));
                return;
            }
        }

        $data['affected'] = $this->model_tool_egeser_description_fix->getAffectedProducts();

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_intro'] = $this->language->get('text_intro');
        $data['text_empty'] = $this->language->get('text_empty');
        $data['text_old'] = $this->language->get('text_old');
        $data['text_new'] = $this->language->get('text_new');
        $data['button_apply'] = $this->language->get('button_apply');
        $data['column_product'] = $this->language->get('column_product');

        $data['token'] = $this->session->data['token'];
        $data['action'] = $this->url->link('tool/egeser_description_fix', 'token=' . $this->session->data['token'], true);

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true));
        $data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'), 'href' => $data['action']);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('tool/egeser_description_fix.tpl', $data));
    }

    protected function validatePermission() {
        if (!$this->user->hasPermission('modify', 'catalog/product')) {
            $this->error['warning'] = 'Uyarı: Ürünleri düzenleme yetkiniz yok.';
            return false;
        }
        return true;
    }
}
