<?php
class ControllerExtensionModuleEgeserVisualManager extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_visual_manager');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        $this->load->model('tool/image');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['egeser_visual_manager_version'] = time();
            $this->model_setting_setting->editSetting('egeser_visual_manager', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/egeser_visual_manager', 'token=' . $this->session->data['token'], true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_selector'] = $this->language->get('text_selector');
        $data['text_textmatch'] = $this->language->get('text_textmatch');
        $data['text_imgsrc'] = $this->language->get('text_imgsrc');
        $data['text_background'] = $this->language->get('text_background');
        $data['text_cover'] = $this->language->get('text_cover');
        $data['text_contain'] = $this->language->get('text_contain');

        $keys = array(
            'entry_status','entry_preview_only','entry_label','entry_match_type','entry_target','entry_apply_type',
            'entry_image','entry_alt','entry_fit','entry_position','entry_recommended','entry_rule_status',
            'help_preview_only','help_target','help_recommended','button_save','button_cancel','button_add_rule','button_remove'
        );
        foreach ($keys as $key) $data[$key] = $this->language->get($key);

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true));
        $data['breadcrumbs'][] = array('text' => $this->language->get('text_extension'), 'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
        $data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/module/egeser_visual_manager', 'token=' . $this->session->data['token'], true));

        $data['action'] = $this->url->link('extension/module/egeser_visual_manager', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
        $data['token'] = $this->session->data['token'];

        $data['egeser_visual_manager_status'] = isset($this->request->post['egeser_visual_manager_status']) ? $this->request->post['egeser_visual_manager_status'] : $this->config->get('egeser_visual_manager_status');
        $data['egeser_visual_manager_preview_only'] = isset($this->request->post['egeser_visual_manager_preview_only']) ? $this->request->post['egeser_visual_manager_preview_only'] : $this->config->get('egeser_visual_manager_preview_only');

        if (isset($this->request->post['egeser_visual_manager_rules'])) {
            $rules = $this->request->post['egeser_visual_manager_rules'];
        } else {
            $rules = $this->config->get('egeser_visual_manager_rules');
        }
        if (!is_array($rules) || !$rules) $rules = $this->getDefaultRules();

        $data['rules'] = array();
        foreach ($rules as $rule) {
            $image = isset($rule['image']) ? trim($rule['image']) : '';
            $thumb = $this->model_tool_image->resize('no_image.png', 180, 100);
            $dimension = '-';
            if ($image && is_file(DIR_IMAGE . $image)) {
                $thumb = $this->model_tool_image->resize($image, 180, 100);
                $size = @getimagesize(DIR_IMAGE . $image);
                if ($size) $dimension = $size[0] . ' × ' . $size[1] . ' px';
            }
            $rule['thumb'] = $thumb;
            $rule['dimension'] = $dimension;
            $data['rules'][] = $rule;
        }
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 180, 100);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/egeser_visual_manager', $data));
    }

    public function install() {
        $this->load->model('user/user_group');
        $this->load->model('setting/setting');
        $group_id = $this->user->getGroupId();
        $this->model_user_user_group->addPermission($group_id, 'access', 'extension/module/egeser_visual_manager');
        $this->model_user_user_group->addPermission($group_id, 'modify', 'extension/module/egeser_visual_manager');

        $this->model_setting_setting->editSetting('egeser_visual_manager', array(
            'egeser_visual_manager_status' => 1,
            'egeser_visual_manager_preview_only' => 1,
            'egeser_visual_manager_rules' => $this->getDefaultRules(),
            'egeser_visual_manager_version' => time()
        ));
    }

    public function uninstall() {
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('egeser_visual_manager');
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_visual_manager')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    private function getDefaultRules() {
        return array(
            array('status'=>1,'label'=>'Ana Sayfa • Tek Katlı Kartı','match_type'=>'text','target'=>'Tek Katlı Prefabrik Evler','apply_type'=>'img','image'=>'','alt'=>'Tek Katlı Prefabrik Evler','fit'=>'cover','position'=>'50% 50%','recommended'=>'1600 × 700 px'),
            array('status'=>1,'label'=>'Ana Sayfa • Çift Katlı Kartı','match_type'=>'text','target'=>'Çift Katlı Prefabrik Evler','apply_type'=>'img','image'=>'','alt'=>'Çift Katlı Prefabrik Evler','fit'=>'cover','position'=>'50% 50%','recommended'=>'1600 × 700 px'),
            array('status'=>0,'label'=>'Ana Sayfa • Tüm Modeller Kartı','match_type'=>'text','target'=>'Prefabrik Ev ve Yapılar','apply_type'=>'background','image'=>'','alt'=>'','fit'=>'cover','position'=>'50% 50%','recommended'=>'1600 × 700 px'),
            array('status'=>0,'label'=>'Hero Slider • 1. Görsel','match_type'=>'selector','target'=>'.egeser-hero-slider-stage .egeser-hero-slide:nth-child(1) img','apply_type'=>'img','image'=>'','alt'=>'Prefabrik Ev','fit'=>'cover','position'=>'50% 50%','recommended'=>'1200 × 1200 px'),
            array('status'=>0,'label'=>'Hero Slider • 2. Görsel','match_type'=>'selector','target'=>'.egeser-hero-slider-stage .egeser-hero-slide:nth-child(2) img','apply_type'=>'img','image'=>'','alt'=>'Prefabrik Ev','fit'=>'cover','position'=>'50% 50%','recommended'=>'1200 × 1200 px'),
            array('status'=>0,'label'=>'Hero Slider • 3. Görsel','match_type'=>'selector','target'=>'.egeser-hero-slider-stage .egeser-hero-slide:nth-child(3) img','apply_type'=>'img','image'=>'','alt'=>'Prefabrik Ev','fit'=>'cover','position'=>'50% 50%','recommended'=>'1200 × 1200 px')
        );
    }
}
