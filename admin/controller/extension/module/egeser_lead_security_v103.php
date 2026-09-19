<?php
class ControllerExtensionModuleEgeserLeadSecurityV103 extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_lead_security_v103');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $post = $this->request->post;
            $post['egeser_lead_security_v103_lifetime'] = max(5, min(60, (int)$post['egeser_lead_security_v103_lifetime']));
            $this->model_setting_setting->editSetting('egeser_lead_security_v103', $post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_lifetime'] = $this->language->get('entry_lifetime');
        $data['help_lifetime'] = $this->language->get('help_lifetime');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        $data['egeser_lead_security_v103_status'] = isset($this->request->post['egeser_lead_security_v103_status'])
            ? $this->request->post['egeser_lead_security_v103_status']
            : $this->config->get('egeser_lead_security_v103_status');

        $data['egeser_lead_security_v103_lifetime'] = isset($this->request->post['egeser_lead_security_v103_lifetime'])
            ? $this->request->post['egeser_lead_security_v103_lifetime']
            : ($this->config->get('egeser_lead_security_v103_lifetime') ? $this->config->get('egeser_lead_security_v103_lifetime') : 20);

        $data['action'] = $this->url->link('extension/module/egeser_lead_security_v103', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/egeser_lead_security_v103', $data));
    }

    public function install() {
        $this->load->model('extension/module/egeser_lead_security_v103');
        $this->model_extension_module_egeser_lead_security_v103->install();

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('egeser_lead_security_v103', array(
            'egeser_lead_security_v103_status' => 1,
            'egeser_lead_security_v103_lifetime' => 20
        ));
    }

    public function uninstall() {
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('egeser_lead_security_v103');
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_lead_security_v103')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
}
