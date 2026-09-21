<?php
class ControllerExtensionModuleEgeserReferences extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_references');
        $this->load->model('extension/module');
        $this->load->model('tool/image');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_extension_module->addModule('egeser_references', $this->cleanData($this->request->post));
            } else {
                $this->model_extension_module->editModule(
                    (int)$this->request->get['module_id'],
                    $this->cleanData($this->request->post)
                );
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect(
                $this->url->link(
                    'extension/extension',
                    'token=' . $this->session->data['token'] . '&type=module',
                    true
                )
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_title'] = $this->language->get('entry_title');
        $data['entry_subtitle'] = $this->language->get('entry_subtitle');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link(
                'extension/module/egeser_references',
                'token=' . $this->session->data['token'] .
                (isset($this->request->get['module_id']) ? '&module_id=' . (int)$this->request->get['module_id'] : ''),
                true
            )
        );

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link(
                'extension/module/egeser_references',
                'token=' . $this->session->data['token'],
                true
            );
        } else {
            $data['action'] = $this->url->link(
                'extension/module/egeser_references',
                'token=' . $this->session->data['token'] . '&module_id=' . (int)$this->request->get['module_id'],
                true
            );
        }

        $data['cancel'] = $this->url->link(
            'extension/extension',
            'token=' . $this->session->data['token'] . '&type=module',
            true
        );

        $module_info = array();

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_extension_module->getModule((int)$this->request->get['module_id']);
        }

        $data['name'] = isset($this->request->post['name'])
            ? $this->request->post['name']
            : (isset($module_info['name']) ? $module_info['name'] : 'Ana Sayfa Referansları');

        $data['status'] = isset($this->request->post['status'])
            ? (int)$this->request->post['status']
            : (isset($module_info['status']) ? (int)$module_info['status'] : 1);

        $data['title'] = isset($this->request->post['title'])
            ? $this->request->post['title']
            : (isset($module_info['title']) ? $module_info['title'] : 'Gerçek uygulamalar güvenin en güçlü kanıtıdır.');

        $data['subtitle'] = isset($this->request->post['subtitle'])
            ? $this->request->post['subtitle']
            : (isset($module_info['subtitle']) ? $module_info['subtitle'] : 'Tamamlanan bireysel ve kurumsal prefabrik yapı projelerinden seçili referanslar.');

        $projects = isset($this->request->post['projects'])
            ? $this->request->post['projects']
            : (isset($module_info['projects']) && is_array($module_info['projects']) ? $module_info['projects'] : array());

        $defaults = array(
            'enabled' => 1,
            'image' => '',
            'eyebrow' => '',
            'type' => '',
            'title' => '',
            'description' => '',
            'location' => '',
            'size' => '',
            'link' => ''
        );

        $data['projects'] = array();

        for ($i = 0; $i < 24; $i++) {
            $project = isset($projects[$i]) && is_array($projects[$i])
                ? array_merge($defaults, $projects[$i])
                : $defaults;

            if (!empty($project['image']) && is_file(DIR_IMAGE . $project['image'])) {
                $project['thumb'] = $this->model_tool_image->resize($project['image'], 220, 220);
            } else {
                $project['thumb'] = $this->model_tool_image->resize('no_image.png', 220, 220);
            }

            $data['projects'][$i] = $project;
        }

        $partners = isset($this->request->post['partners'])
            ? $this->request->post['partners']
            : (isset($module_info['partners']) && is_array($module_info['partners']) ? $module_info['partners'] : array());

        $partner_defaults = array(
            'enabled' => 1,
            'image' => '',
            'name' => '',
            'description' => '',
            'link' => ''
        );

        $data['partners'] = array();

        for ($i = 0; $i < 8; $i++) {
            $partner = isset($partners[$i]) && is_array($partners[$i])
                ? array_merge($partner_defaults, $partners[$i])
                : $partner_defaults;

            if (!empty($partner['image']) && is_file(DIR_IMAGE . $partner['image'])) {
                $partner['thumb'] = $this->model_tool_image->resize($partner['image'], 180, 90);
            } else {
                $partner['thumb'] = $this->model_tool_image->resize('no_image.png', 180, 90);
            }

            $data['partners'][$i] = $partner;
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 220, 220);
        $data['placeholder_partner'] = $this->model_tool_image->resize('no_image.png', 180, 90);
        $data['token'] = $this->session->data['token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput(
            $this->load->view('extension/module/egeser_references.tpl', $data)
        );
    }

    public function install() {
        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission(
            $this->user->getGroupId(),
            'access',
            'extension/module/egeser_references'
        );

        $this->model_user_user_group->addPermission(
            $this->user->getGroupId(),
            'modify',
            'extension/module/egeser_references'
        );
    }

    private function cleanData($input) {
        $output = array();

        $output['name'] = isset($input['name']) ? trim(strip_tags($input['name'])) : 'Ana Sayfa Referansları';
        $output['status'] = !empty($input['status']) ? 1 : 0;
        $output['title'] = isset($input['title']) ? trim(strip_tags($input['title'])) : '';
        $output['subtitle'] = isset($input['subtitle']) ? trim(strip_tags($input['subtitle'])) : '';
        $output['projects'] = array();

        for ($i = 0; $i < 24; $i++) {
            $row = isset($input['projects'][$i]) && is_array($input['projects'][$i]) ? $input['projects'][$i] : array();

            $output['projects'][$i] = array(
                'enabled' => !empty($row['enabled']) ? 1 : 0,
                'image' => isset($row['image']) ? trim($row['image']) : '',
                'eyebrow' => isset($row['eyebrow']) ? trim(strip_tags($row['eyebrow'])) : '',
                'type' => isset($row['type']) ? trim(strip_tags($row['type'])) : '',
                'title' => isset($row['title']) ? trim(strip_tags($row['title'])) : '',
                'description' => isset($row['description']) ? trim(strip_tags($row['description'])) : '',
                'location' => isset($row['location']) ? trim(strip_tags($row['location'])) : '',
                'size' => isset($row['size']) ? trim(strip_tags($row['size'])) : '',
                'link' => isset($row['link']) ? trim($row['link']) : ''
            );
        }

        $output['partners'] = array();

        for ($i = 0; $i < 8; $i++) {
            $row = isset($input['partners'][$i]) && is_array($input['partners'][$i]) ? $input['partners'][$i] : array();

            $output['partners'][$i] = array(
                'enabled' => !empty($row['enabled']) ? 1 : 0,
                'image' => isset($row['image']) ? trim($row['image']) : '',
                'name' => isset($row['name']) ? trim(strip_tags($row['name'])) : '',
                'description' => isset($row['description']) ? trim(strip_tags($row['description'])) : '',
                'link' => isset($row['link']) ? trim($row['link']) : ''
            );
        }

        return $output;
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_references')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $name = isset($this->request->post['name']) ? trim($this->request->post['name']) : '';

        if (utf8_strlen($name) < 3 || utf8_strlen($name) > 64) {
            $this->error['name'] = $this->language->get('error_name');
        }

        return !$this->error;
    }
}
