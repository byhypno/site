<?php
class ControllerCatalogEgeserFolderOrganizer extends Controller {
    /* Şimdilik sadece bu iki kategori düzenleniyor; diğer ürün
       tipleri (ofis, yemekhane, vb.) bilinçli olarak kapsam dışı. */
    private $allowed_categories = array('Tek Katlı Prefabrik Evler', 'Çift Katlı Prefabrik Evler');

    private function filterToScope($items) {
        $filtered = array();
        foreach ($items as $item) {
            if (in_array($item['category_name'], $this->allowed_categories, true)) { $filtered[] = $item; }
        }
        return $filtered;
    }

    public function index() {
        $this->document->setTitle('Ürün Görsel Klasörlerini Düzenle');

        if (!$this->user->hasPermission('access', 'catalog/product')) {
            $this->response->redirect($this->url->link('error/permission', 'token=' . $this->session->data['token'], true));
            return;
        }

        $this->load->model('catalog/egeser_folder_organizer');

        $data = array('error_warning' => '');

        $scan = $this->model_catalog_egeser_folder_organizer->scan();

        $data['items'] = $this->filterToScope($scan['items']);

        $ready_count = 0;
        foreach ($data['items'] as $item) { if ($item['status'] === 'ready') { $ready_count++; } }
        $data['ready_count'] = $ready_count;

        $log = $this->model_catalog_egeser_folder_organizer->getLatestLog();
        $data['latest_log'] = $log;

        $data['action'] = $this->url->link('catalog/egeser_folder_organizer/apply', 'token=' . $this->session->data['token'], true);
        $data['revert_action'] = $this->url->link('catalog/egeser_folder_organizer/revert', 'token=' . $this->session->data['token'], true);
        $data['token'] = $this->session->data['token'];

        $data['breadcrumbs'] = array(
            array('text' => 'Ana Sayfa', 'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)),
            array('text' => 'Görsel Klasörlerini Düzenle', 'href' => $this->url->link('catalog/egeser_folder_organizer', 'token=' . $this->session->data['token'], true))
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/egeser_folder_organizer.tpl', $data));
    }

    public function apply() {
        $json = array('success' => false, 'results' => array(), 'error' => '');

        if (!$this->user->hasPermission('modify', 'catalog/product')) {
            $json['error'] = 'Bu işlem için ürün düzenleme yetkisi gerekiyor.';
            $this->respondJson($json);
            return;
        }

        $this->load->model('catalog/egeser_folder_organizer');

        $submitted = isset($this->request->post['items']) && is_array($this->request->post['items']) ? $this->request->post['items'] : array();

        if (!$submitted) {
            $json['error'] = 'Hiçbir ürün seçilmedi.';
            $this->respondJson($json);
            return;
        }

        // Güvenlik: istemciden gelen her satırı, aynı anda alınan taze bir
        // taramayla ve kapsam listesine karşı doğrula — sayfa açıldığından
        // beri bir şey değişmiş olabilir.
        $fresh = $this->model_catalog_egeser_folder_organizer->scan();
        $fresh_by_product = array();
        foreach ($this->filterToScope($fresh['items']) as $item) { $fresh_by_product[$item['product_id']] = $item; }

        $batch = array();
        $results = array();

        foreach ($submitted as $product_id) {
            $product_id = (int)$product_id;

            if (!isset($fresh_by_product[$product_id]) || $fresh_by_product[$product_id]['status'] !== 'ready') {
                $results[] = array('product_id' => $product_id, 'success' => false, 'error' => 'Bu ürün artık uygun durumda değil (sayfayı yenileyip tekrar deneyin).');
                continue;
            }

            $outcome = $this->model_catalog_egeser_folder_organizer->applyOne($product_id);
            $results[] = $outcome;

            if (!empty($outcome['success'])) {
                $batch[] = $outcome;
            }
        }

        if ($batch) {
            $this->model_catalog_egeser_folder_organizer->writeLog($batch);
        }

        $json['success'] = true;
        $json['results'] = $results;

        $this->respondJson($json);
    }

    public function revert() {
        $json = array('success' => false, 'results' => array(), 'error' => '');

        if (!$this->user->hasPermission('modify', 'catalog/product')) {
            $json['error'] = 'Bu işlem için ürün düzenleme yetkisi gerekiyor.';
            $this->respondJson($json);
            return;
        }

        $this->load->model('catalog/egeser_folder_organizer');

        $log = $this->model_catalog_egeser_folder_organizer->getLatestLog();

        if (!$log || empty($log['entries'])) {
            $json['error'] = 'Geri alınacak bir işlem bulunamadı.';
            $this->respondJson($json);
            return;
        }

        $results = array();

        foreach ($log['entries'] as $entry) {
            $outcome = $this->model_catalog_egeser_folder_organizer->revertOne($entry);
            $outcome['product_id'] = isset($entry['product_id']) ? $entry['product_id'] : null;
            $outcome['product_name'] = isset($entry['product_name']) ? $entry['product_name'] : '';
            $results[] = $outcome;
        }

        $this->model_catalog_egeser_folder_organizer->markLogReverted($log['filename']);

        $json['success'] = true;
        $json['results'] = $results;

        $this->respondJson($json);
    }

    private function respondJson($json) {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
