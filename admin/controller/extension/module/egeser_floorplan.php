<?php
class ControllerExtensionModuleEgeserFloorplan extends Controller {
    private $error = array();

    public function install() {
        $this->load->model('extension/module/egeser_floorplan');
        $this->model_extension_module_egeser_floorplan->install();
    }

    public function index() {
        $this->load->language('extension/module/egeser_floorplan');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/egeser_floorplan');
        $this->load->model('tool/image');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_select_product'] = $this->language->get('text_select_product');
        $data['text_no_plans'] = $this->language->get('text_no_plans');
        $data['entry_product'] = $this->language->get('entry_product');
        $data['entry_title'] = $this->language->get('entry_title');
        $data['entry_image'] = $this->language->get('entry_image');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_remove'] = $this->language->get('button_remove');

        $token = isset($this->session->data['token']) ? $this->session->data['token'] : '';
        $product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;
            $plans = isset($this->request->post['floorplans']) ? $this->request->post['floorplans'] : array();
            if ($product_id) {
                $this->model_extension_module_egeser_floorplan->savePlans($product_id, $plans);
                $this->session->data['success'] = $this->language->get('text_success');
            }
            $this->response->redirect($this->url->link('extension/module/egeser_floorplan', 'token=' . $token . '&product_id=' . $product_id, true));
        }

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
        unset($this->session->data['success']);

        $data['products'] = $this->model_extension_module_egeser_floorplan->getProducts();
        $data['product_id'] = $product_id;
        $data['floorplans'] = array();
        if ($product_id) {
            foreach ($this->model_extension_module_egeser_floorplan->getPlans($product_id) as $plan) {
                $thumb = (!empty($plan['image']) && is_file(DIR_IMAGE . $plan['image'])) ? $this->model_tool_image->resize($plan['image'], 120, 90) : $this->model_tool_image->resize('no_image.png', 120, 90);
                $data['floorplans'][] = array(
                    'floorplan_id' => $plan['floorplan_id'],
                    'title' => $plan['title'],
                    'image' => $plan['image'],
                    'thumb' => $thumb,
                    'sort_order' => $plan['sort_order']
                );
            }
        }
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 120, 90);
        $data['action'] = $this->url->link('extension/module/egeser_floorplan', 'token=' . $token . ($product_id ? '&product_id=' . $product_id : ''), true);
        $data['load_product_url'] = $this->url->link('extension/module/egeser_floorplan', 'token=' . $token, true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $token . '&type=module', true);
        $data['token'] = $token;

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'token=' . $token, true));
        $data['breadcrumbs'][] = array('text' => $this->language->get('text_extension'), 'href' => $this->url->link('extension/extension', 'token=' . $token . '&type=module', true));
        $data['breadcrumbs'][] = array('text' => $data['heading_title'], 'href' => $this->url->link('extension/module/egeser_floorplan', 'token=' . $token, true));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/egeser_floorplan.tpl', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_floorplan')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
}
