<?php
class ControllerExtensionModuleEgeserLeadManager extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_lead_manager');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('egeser_lead_manager', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        $data['egeser_lead_manager_status'] = isset($this->request->post['egeser_lead_manager_status'])
            ? $this->request->post['egeser_lead_manager_status']
            : $this->config->get('egeser_lead_manager_status');

        $data['action'] = $this->url->link('extension/module/egeser_lead_manager', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
        $data['lead_list'] = $this->url->link('sale/egeser_lead', 'token=' . $this->session->data['token'], true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/egeser_lead_manager', $data));
    }

    public function install() {
        $this->load->model('extension/module/egeser_lead_manager');
        $this->model_extension_module_egeser_lead_manager->install();

        $this->load->model('user/user_group');
        $group_id = (int)$this->user->getGroupId();

        $this->model_user_user_group->addPermission($group_id, 'access', 'sale/egeser_lead');
        $this->model_user_user_group->addPermission($group_id, 'modify', 'sale/egeser_lead');
        $this->model_user_user_group->addPermission($group_id, 'access', 'extension/module/egeser_lead_manager');
        $this->model_user_user_group->addPermission($group_id, 'modify', 'extension/module/egeser_lead_manager');

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('egeser_lead_manager', array(
            'egeser_lead_manager_status' => 1
        ));
    }

    public function uninstall() {
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('egeser_lead_manager');
        // Lead kayıtları ve yönetim alanları bilinçli olarak silinmez.
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_lead_manager')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
}
