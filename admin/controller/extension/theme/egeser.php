<?php
class ControllerExtensionThemeEgeser extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/theme/egeser');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        $store_id = isset($this->request->get['store_id']) ? (int)$this->request->get['store_id'] : 0;

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('egeser', $this->request->post, $store_id);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=theme', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['entry_directory'] = $this->language->get('entry_directory');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_product_limit'] = $this->language->get('entry_product_limit');
        $data['entry_description_length'] = $this->language->get('entry_description_length');
        $data['entry_image_sizes'] = $this->language->get('entry_image_sizes');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array('text'=>$this->language->get('text_home'),'href'=>$this->url->link('common/dashboard','token='.$this->session->data['token'],true));
        $data['breadcrumbs'][] = array('text'=>$this->language->get('text_extension'),'href'=>$this->url->link('extension/extension','token='.$this->session->data['token'].'&type=theme',true));
        $data['breadcrumbs'][] = array('text'=>$this->language->get('heading_title'),'href'=>$this->url->link('extension/theme/egeser','token='.$this->session->data['token'].'&store_id='.$store_id,true));

        $data['action'] = $this->url->link('extension/theme/egeser', 'token=' . $this->session->data['token'] . '&store_id=' . $store_id, true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=theme', true);

        $setting_info = array();
        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $setting_info = $this->model_setting_setting->getSetting('egeser', $store_id);
        }

        $defaults = array(
            'egeser_directory' => 'egeser',
            'egeser_status' => 1,
            'egeser_product_limit' => 15,
            'egeser_product_description_length' => 100,
            'egeser_image_category_width' => 760,
            'egeser_image_category_height' => 500,
            'egeser_image_thumb_width' => 900,
            'egeser_image_thumb_height' => 675,
            'egeser_image_popup_width' => 1600,
            'egeser_image_popup_height' => 1200,
            'egeser_image_product_width' => 480,
            'egeser_image_product_height' => 360,
            'egeser_image_additional_width' => 160,
            'egeser_image_additional_height' => 120,
            'egeser_image_related_width' => 480,
            'egeser_image_related_height' => 360,
            'egeser_image_compare_width' => 180,
            'egeser_image_compare_height' => 135,
            'egeser_image_wishlist_width' => 180,
            'egeser_image_wishlist_height' => 135,
            'egeser_image_cart_width' => 120,
            'egeser_image_cart_height' => 90,
            'egeser_image_location_width' => 320,
            'egeser_image_location_height' => 120
        );

        foreach ($defaults as $key => $default) {
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } elseif (isset($setting_info[$key])) {
                $data[$key] = $setting_info[$key];
            } else {
                $data[$key] = $default;
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/theme/egeser', $data));
    }

    public function install() {
        if (!$this->user->hasPermission('modify', 'extension/theme/egeser')) return;

        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/theme/egeser');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/theme/egeser');

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('egeser', array(
            'egeser_directory'=>'egeser',
            'egeser_status'=>1,
            'egeser_product_limit'=>15,
            'egeser_product_description_length'=>100,
            'egeser_image_category_width'=>760,
            'egeser_image_category_height'=>500,
            'egeser_image_thumb_width'=>900,
            'egeser_image_thumb_height'=>675,
            'egeser_image_popup_width'=>1600,
            'egeser_image_popup_height'=>1200,
            'egeser_image_product_width'=>480,
            'egeser_image_product_height'=>360,
            'egeser_image_additional_width'=>160,
            'egeser_image_additional_height'=>120,
            'egeser_image_related_width'=>480,
            'egeser_image_related_height'=>360,
            'egeser_image_compare_width'=>180,
            'egeser_image_compare_height'=>135,
            'egeser_image_wishlist_width'=>180,
            'egeser_image_wishlist_height'=>135,
            'egeser_image_cart_width'=>120,
            'egeser_image_cart_height'=>90,
            'egeser_image_location_width'=>320,
            'egeser_image_location_height'=>120
        ), 0);
    }

    public function uninstall() {
        // Rollback sırasında tema ayarları bilinçli olarak korunur.
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/theme/egeser')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['egeser_directory']) && $this->request->post['egeser_directory'] !== 'egeser') {
            $this->error['warning'] = $this->language->get('error_directory');
        }

        $required_numeric = array(
            'egeser_product_limit','egeser_product_description_length',
            'egeser_image_category_width','egeser_image_category_height',
            'egeser_image_thumb_width','egeser_image_thumb_height',
            'egeser_image_popup_width','egeser_image_popup_height',
            'egeser_image_product_width','egeser_image_product_height',
            'egeser_image_additional_width','egeser_image_additional_height',
            'egeser_image_related_width','egeser_image_related_height',
            'egeser_image_compare_width','egeser_image_compare_height',
            'egeser_image_wishlist_width','egeser_image_wishlist_height',
            'egeser_image_cart_width','egeser_image_cart_height',
            'egeser_image_location_width','egeser_image_location_height'
        );

        foreach ($required_numeric as $key) {
            if (!isset($this->request->post[$key]) || (int)$this->request->post[$key] < 1) {
                $this->error['warning'] = $this->language->get('error_required_dimensions');
                break;
            }
        }

        return !$this->error;
    }
}
