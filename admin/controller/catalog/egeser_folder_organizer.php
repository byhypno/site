<?php
class ControllerCatalogEgeserFolderOrganizer extends Controller {
    public function index() {
        $this->document->setTitle('Ürün Görsel Klasörlerini Düzenle');

        if (!$this->user->hasPermission('access', 'catalog/product')) {
            $this->response->redirect($this->url->link('error/permission', 'token=' . $this->session->data['token'], true));
            return;
        }

        $this->load->model('catalog/egeser_folder_organizer');

        $data = array('error_warning' => '');

        $scan = $this->model_catalog_egeser_folder_organizer->scan();

        $data['base_dir_exists'] = $scan['base_dir_exists'];
        $data['items'] = $scan['items'];

        $ready_count = 0;
        foreach ($scan['items'] as $item) { if ($item['status'] === 'ready') { $ready_count++; } }
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
            $json['error'] = 'Hiçbir klasör seçilmedi.';
            $this->respondJson($json);
            return;
        }

        // Güvenlik: istemciden gelen her satırı, aynı anda alınan taze bir
        // taramayla doğrula — sayfa açıldığından beri bir şey değişmiş olabilir.
        $fresh = $this->model_catalog_egeser_folder_organizer->scan();
        $fresh_by_folder = array();
        foreach ($fresh['items'] as $item) { $fresh_by_folder[$item['folder_id']] = $item; }

        $batch = array();
        $results = array();

        foreach ($submitted as $folder_id) {
            $folder_id = (string)$folder_id;

            if (!isset($fresh_by_folder[$folder_id]) || $fresh_by_folder[$folder_id]['status'] !== 'ready') {
                $results[] = array('folder_id' => $folder_id, 'success' => false, 'error' => 'Bu klasör artık uygun durumda değil (sayfayı yenileyip tekrar deneyin).');
                continue;
            }

            $expected = $fresh_by_folder[$folder_id];

            $outcome = $this->model_catalog_egeser_folder_organizer->applyOne($folder_id, $expected['new_slug'], $expected['product_id']);
            $outcome['folder_id'] = $folder_id;
            $outcome['product_name'] = $expected['product_name'];
            $results[] = $outcome;

            if (!empty($outcome['success'])) {
                $batch[] = array(
                    'folder_id'    => $folder_id,
                    'new_slug'     => $expected['new_slug'],
                    'product_id'   => $expected['product_id'],
                    'product_name' => $expected['product_name']
                );
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
            $outcome = $this->model_catalog_egeser_folder_organizer->revertOne($entry['folder_id'], $entry['new_slug']);
            $outcome['folder_id'] = $entry['folder_id'];
            $outcome['new_slug'] = $entry['new_slug'];
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
