<?php
class ControllerCatalogEgeserImageManager extends Controller {
    public function index() {
        $this->document->setTitle('Ürün Görsellerini Değiştir');

        if (!$this->user->hasPermission('access', 'catalog/product')) {
            $this->response->redirect($this->url->link('error/permission', 'token=' . $this->session->data['token'], true));
            return;
        }

        $this->load->model('catalog/egeser_image_manager');
        $this->load->model('tool/image');

        $filter_name = isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : '';
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        if ($page < 1) { $page = 1; }

        $limit = 30;

        $filter_data = array(
            'filter_name' => $filter_name,
            'start'       => ($page - 1) * $limit,
            'limit'       => $limit
        );

        $product_total = $this->model_catalog_egeser_image_manager->getTotalProducts($filter_data);
        $products = $this->model_catalog_egeser_image_manager->getProducts($filter_data);

        $data['products'] = array();

        foreach ($products as $p) {
            if (is_file(DIR_IMAGE . $p['image'])) {
                $thumb = $this->model_tool_image->resize($p['image'], 120, 120);
            } else {
                $thumb = $this->model_tool_image->resize('no_image.png', 120, 120);
            }

            $additional = array();
            foreach ($this->model_catalog_egeser_image_manager->getAdditionalImages($p['product_id']) as $a) {
                $additional[] = array(
                    'product_image_id' => $a['product_image_id'],
                    'thumb'            => is_file(DIR_IMAGE . $a['image']) ? $this->model_tool_image->resize($a['image'], 80, 80) : $this->model_tool_image->resize('no_image.png', 80, 80)
                );
            }

            $data['products'][] = array(
                'product_id' => $p['product_id'],
                'name'       => $p['name'],
                'model'      => $p['model'],
                'thumb'      => $thumb,
                'additional' => $additional
            );
        }

        $url = '';
        if ($filter_name) { $url .= '&filter_name=' . urlencode(html_entity_decode($filter_name, ENT_QUOTES, 'UTF-8')); }

        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('catalog/egeser_image_manager', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['filter_name'] = $filter_name;
        $data['product_total'] = $product_total;

        $data['filter_action'] = $this->url->link('catalog/egeser_image_manager', 'token=' . $this->session->data['token'], true);
        $data['token'] = $this->session->data['token'];

        // Bu URL'ler <script> içinde ham JS string olarak kullanılacak; url->link()
        // href için "&amp;" üretir, JS'te gönderilince token kaybolur ve oturum
        // geçersiz görünür — bu yüzden burada html_entity_decode ile düzeltiliyor.
        $data['upload_main_action'] = html_entity_decode($this->url->link('catalog/egeser_image_manager/uploadMain', 'token=' . $this->session->data['token'], true), ENT_QUOTES, 'UTF-8');
        $data['upload_additional_action'] = html_entity_decode($this->url->link('catalog/egeser_image_manager/uploadAdditional', 'token=' . $this->session->data['token'], true), ENT_QUOTES, 'UTF-8');
        $data['remove_additional_action'] = html_entity_decode($this->url->link('catalog/egeser_image_manager/removeAdditional', 'token=' . $this->session->data['token'], true), ENT_QUOTES, 'UTF-8');

        $data['breadcrumbs'] = array(
            array('text' => 'Ana Sayfa', 'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)),
            array('text' => 'Ürün Görsellerini Değiştir', 'href' => $this->url->link('catalog/egeser_image_manager', 'token=' . $this->session->data['token'], true))
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/egeser_image_manager.tpl', $data));
    }

    public function uploadMain() {
        $json = array('success' => false, 'error' => '');

        if (!$this->user->hasPermission('modify', 'catalog/product')) {
            $json['error'] = 'Bu işlem için ürün düzenleme yetkisi gerekiyor.';
            $this->respondJson($json);
            return;
        }

        $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;
        $file = $this->getUploadedFile('file');

        if (!$product_id || !$file) {
            $json['error'] = 'Ürün veya dosya eksik.';
            $this->respondJson($json);
            return;
        }

        if ((int)$file['error'] !== UPLOAD_ERR_OK) {
            $json['error'] = 'Dosya yüklenirken bir hata oluştu.';
            $this->respondJson($json);
            return;
        }

        $this->load->model('catalog/egeser_image_manager');
        $this->load->model('tool/image');

        $result = $this->model_catalog_egeser_image_manager->replaceMainImage($product_id, $file['tmp_name'], $file['name']);

        if (!empty($result['success'])) {
            $result['thumb'] = $this->model_tool_image->resize($result['image'], 120, 120);
        }

        $this->respondJson($result);
    }

    public function uploadAdditional() {
        $json = array('success' => false, 'error' => '');

        if (!$this->user->hasPermission('modify', 'catalog/product')) {
            $json['error'] = 'Bu işlem için ürün düzenleme yetkisi gerekiyor.';
            $this->respondJson($json);
            return;
        }

        $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;
        $file = $this->getUploadedFile('file');

        if (!$product_id || !$file) {
            $json['error'] = 'Ürün veya dosya eksik.';
            $this->respondJson($json);
            return;
        }

        if ((int)$file['error'] !== UPLOAD_ERR_OK) {
            $json['error'] = 'Dosya yüklenirken bir hata oluştu.';
            $this->respondJson($json);
            return;
        }

        $this->load->model('catalog/egeser_image_manager');
        $this->load->model('tool/image');

        $result = $this->model_catalog_egeser_image_manager->addAdditionalImage($product_id, $file['tmp_name'], $file['name']);

        if (!empty($result['success'])) {
            $result['thumb'] = $this->model_tool_image->resize($result['image'], 80, 80);
        }

        $this->respondJson($result);
    }

    public function removeAdditional() {
        $json = array('success' => false, 'error' => '');

        if (!$this->user->hasPermission('modify', 'catalog/product')) {
            $json['error'] = 'Bu işlem için ürün düzenleme yetkisi gerekiyor.';
            $this->respondJson($json);
            return;
        }

        $product_image_id = isset($this->request->post['product_image_id']) ? (int)$this->request->post['product_image_id'] : 0;

        if (!$product_image_id) {
            $json['error'] = 'Görsel belirtilmedi.';
            $this->respondJson($json);
            return;
        }

        $this->load->model('catalog/egeser_image_manager');

        $result = $this->model_catalog_egeser_image_manager->removeAdditionalImage($product_image_id);

        $this->respondJson($result);
    }

    private function getUploadedFile($key) {
        if (empty($this->request->files[$key]) || !is_array($this->request->files[$key])) {
            return false;
        }

        return $this->request->files[$key];
    }

    private function respondJson($json) {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
