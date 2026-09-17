<?php
class ControllerExtensionModuleEgeserHeroSlider extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_hero_slider');
        $this->load->model('extension/module');
        $this->load->model('tool/image');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $clean = $this->cleanData($this->request->post);

            if (!isset($this->request->get['module_id'])) {
                $this->model_extension_module->addModule('egeser_hero_slider', $clean);
            } else {
                $this->model_extension_module->editModule((int)$this->request->get['module_id'], $clean);
            }

            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
        }

        $keys = array(
            'heading_title','text_edit','text_enabled','text_disabled','text_home','text_extension',
            'entry_name','entry_status','entry_interval','entry_transition','entry_pause_hover',
            'button_save','button_cancel','text_yes','text_no','help_interval','help_transition'
        );
        foreach ($keys as $key) {
            $data[$key] = $this->language->get($key);
        }

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';
        $data['error_slides'] = isset($this->error['slides']) ? $this->error['slides'] : '';

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array('text' => $data['text_home'], 'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true));
        $data['breadcrumbs'][] = array('text' => $data['text_extension'], 'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
        $data['breadcrumbs'][] = array('text' => $data['heading_title'], 'href' => $this->url->link('extension/module/egeser_hero_slider', 'token=' . $this->session->data['token'] . (isset($this->request->get['module_id']) ? '&module_id=' . (int)$this->request->get['module_id'] : ''), true));

        $data['action'] = $this->url->link('extension/module/egeser_hero_slider', 'token=' . $this->session->data['token'] . (isset($this->request->get['module_id']) ? '&module_id=' . (int)$this->request->get['module_id'] : ''), true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

        $module_info = array();
        if (isset($this->request->get['module_id']) && $this->request->server['REQUEST_METHOD'] != 'POST') {
            $module_info = $this->model_extension_module->getModule((int)$this->request->get['module_id']);
        }

        $src = $this->request->server['REQUEST_METHOD'] == 'POST' ? $this->request->post : $module_info;
        $data['name'] = isset($src['name']) ? $src['name'] : 'Ana Sayfa Hero Görselleri';
        $data['status'] = isset($src['status']) ? (int)$src['status'] : 1;
        $data['interval'] = isset($src['interval']) ? (int)$src['interval'] : 5000;
        $data['transition'] = isset($src['transition']) ? (int)$src['transition'] : 900;
        $data['pause_hover'] = isset($src['pause_hover']) ? (int)$src['pause_hover'] : 1;

        $slides = isset($src['slides']) && is_array($src['slides']) ? $src['slides'] : array();
        $defaults = array('enabled' => 1, 'image' => '', 'alt' => '', 'link' => '', 'sort_order' => 0);
        $data['slides'] = array();

        for ($i = 0; $i < 6; $i++) {
            $slide = isset($slides[$i]) && is_array($slides[$i]) ? array_merge($defaults, $slides[$i]) : $defaults;
            if (!empty($slide['image']) && is_file(DIR_IMAGE . $slide['image'])) {
                $slide['thumb'] = $this->model_tool_image->resize($slide['image'], 240, 160);
            } else {
                $slide['thumb'] = $this->model_tool_image->resize('no_image.png', 240, 160);
            }
            $data['slides'][$i] = $slide;
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 240, 160);
        $data['token'] = $this->session->data['token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/egeser_hero_slider.tpl', $data));
    }

    public function install() {
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/egeser_hero_slider');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/egeser_hero_slider');
    }

    private function cleanData($input) {
        $out = array();
        $out['name'] = isset($input['name']) ? trim(strip_tags($input['name'])) : 'Ana Sayfa Hero Görselleri';
        $out['status'] = !empty($input['status']) ? 1 : 0;
        $out['interval'] = isset($input['interval']) ? max(2500, min(15000, (int)$input['interval'])) : 5000;
        $out['transition'] = isset($input['transition']) ? max(250, min(2500, (int)$input['transition'])) : 900;
        $out['pause_hover'] = !empty($input['pause_hover']) ? 1 : 0;
        $out['slides'] = array();

        for ($i = 0; $i < 6; $i++) {
            $row = isset($input['slides'][$i]) && is_array($input['slides'][$i]) ? $input['slides'][$i] : array();
            $link = isset($row['link']) ? trim($row['link']) : '';
            if ($link !== '' && strpos($link, '/') !== 0 && strpos($link, 'index.php?route=') !== 0 && !preg_match('#^https?://#i', $link)) {
                $link = '';
            }
            $out['slides'][$i] = array(
                'enabled' => !empty($row['enabled']) ? 1 : 0,
                'image' => isset($row['image']) ? trim($row['image']) : '',
                'alt' => isset($row['alt']) ? trim(strip_tags($row['alt'])) : '',
                'link' => $link,
                'sort_order' => isset($row['sort_order']) ? (int)$row['sort_order'] : $i
            );
        }
        return $out;
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_hero_slider')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        $name = isset($this->request->post['name']) ? trim($this->request->post['name']) : '';
        if (utf8_strlen($name) < 3 || utf8_strlen($name) > 64) {
            $this->error['name'] = $this->language->get('error_name');
        }
        $active = 0;
        if (!empty($this->request->post['slides']) && is_array($this->request->post['slides'])) {
            foreach ($this->request->post['slides'] as $row) {
                if (!empty($row['enabled']) && !empty($row['image'])) $active++;
            }
        }
        if ($active < 1) {
            $this->error['slides'] = $this->language->get('error_slides');
        }
        return !$this->error;
    }
}
