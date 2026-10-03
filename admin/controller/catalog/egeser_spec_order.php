<?php
class ControllerCatalogEgeserSpecOrder extends Controller {
    /* Tek doğruluk kaynağı: ürün sayfasındaki Teknik Özellikler
       kutuları ve her kutudaki bilinen satır isimleri. Kaydedilen
       sıralama sadece bu listedeki isimleri içerebilir. */
    private $allowed_buckets = array('Plan ve Model', 'Yapı ve Yalıtım', 'İç Mekân', 'Elektrik ve Tesisat');

    private $allowed_rows = array(
        'Plan ve Model' => array('Alan', 'Oda Sayısı', 'Kat Sayısı', 'Yapı Tipi'),
        'Yapı ve Yalıtım' => array('Duvar Kalınlığı', 'Çatı Sistemi'),
        'İç Mekân' => array('Tavan', 'Zemin', 'PVC Doğrama', 'Cam Sistemi', 'İç Kapılar', 'Dış Kapı', 'Mutfak', 'Banyo'),
        'Elektrik ve Tesisat' => array('Elektrik Tesisatı', 'Sıhhi Tesisat')
    );

    private function getDataPath() {
        /* admin/config.php has no DIR_CATALOG constant; derive the
           storefront root from DIR_APPLICATION (".../admin/"). */
        return rtrim(dirname(rtrim(DIR_APPLICATION, '/')), '/') . '/catalog/view/theme/egeser/data/eg_spec_order.php';
    }

    private function getDefaultOrder() {
        return array(
            'buckets' => $this->allowed_buckets,
            'rows'    => $this->allowed_rows
        );
    }

    private function loadOrder() {
        $path = $this->getDataPath();

        if (is_file($path)) {
            $order = include $path;

            if (is_array($order) && isset($order['buckets']) && isset($order['rows'])) {
                return $order;
            }
        }

        return $this->getDefaultOrder();
    }

    public function index() {
        $this->document->setTitle('Teknik Özellik Sırası');

        if (!$this->user->hasPermission('access', 'catalog/product')) {
            $this->response->redirect($this->url->link('error/permission', 'token=' . $this->session->data['token'], true));
            return;
        }

        $data = array('error_warning' => '', 'success' => '');

        if (isset($this->session->data['egeser_spec_order_success'])) {
            $data['success'] = $this->session->data['egeser_spec_order_success'];
            unset($this->session->data['egeser_spec_order_success']);
        }

        $order = $this->loadOrder();

        $data['buckets'] = array();

        foreach ($order['buckets'] as $bucket_name) {
            if (!in_array($bucket_name, $this->allowed_buckets, true)) { continue; }

            $rows = isset($order['rows'][$bucket_name]) ? $order['rows'][$bucket_name] : array();
            $rows = array_values(array_intersect($rows, $this->allowed_rows[$bucket_name]));

            /* Kayıtlı listede eksik kalan (yeni eklenmiş) satırları sona ekle. */
            foreach ($this->allowed_rows[$bucket_name] as $known_row) {
                if (!in_array($known_row, $rows, true)) { $rows[] = $known_row; }
            }

            $data['buckets'][] = array('name' => $bucket_name, 'rows' => $rows);
        }

        /* Kayıtlı listede eksik kalan (yeni eklenmiş) kutuları sona ekle. */
        $listed_buckets = array_map(function($b) { return $b['name']; }, $data['buckets']);

        foreach ($this->allowed_buckets as $known_bucket) {
            if (!in_array($known_bucket, $listed_buckets, true)) {
                $data['buckets'][] = array('name' => $known_bucket, 'rows' => $this->allowed_rows[$known_bucket]);
            }
        }

        $data['action'] = $this->url->link('catalog/egeser_spec_order/save', 'token=' . $this->session->data['token'], true);
        $data['token'] = $this->session->data['token'];

        $data['breadcrumbs'] = array(
            array('text' => 'Ana Sayfa', 'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)),
            array('text' => 'Teknik Özellik Sırası', 'href' => $this->url->link('catalog/egeser_spec_order', 'token=' . $this->session->data['token'], true))
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/egeser_spec_order.tpl', $data));
    }

    public function save() {
        $json = array('success' => false, 'error' => '');

        if (!$this->user->hasPermission('modify', 'catalog/product')) {
            $json['error'] = 'Bu işlem için ürün düzenleme yetkisi gerekiyor.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $post_buckets = isset($this->request->post['buckets']) && is_array($this->request->post['buckets']) ? $this->request->post['buckets'] : array();
        $post_rows = isset($this->request->post['rows']) && is_array($this->request->post['rows']) ? $this->request->post['rows'] : array();

        $buckets = array();

        foreach ($post_buckets as $bucket_name) {
            if (in_array($bucket_name, $this->allowed_buckets, true) && !in_array($bucket_name, $buckets, true)) {
                $buckets[] = $bucket_name;
            }
        }

        foreach ($this->allowed_buckets as $known_bucket) {
            if (!in_array($known_bucket, $buckets, true)) { $buckets[] = $known_bucket; }
        }

        $rows = array();

        foreach ($this->allowed_rows as $bucket_name => $known_rows) {
            $submitted = isset($post_rows[$bucket_name]) && is_array($post_rows[$bucket_name]) ? $post_rows[$bucket_name] : array();

            $clean = array();

            foreach ($submitted as $row_name) {
                if (in_array($row_name, $known_rows, true) && !in_array($row_name, $clean, true)) {
                    $clean[] = $row_name;
                }
            }

            foreach ($known_rows as $known_row) {
                if (!in_array($known_row, $clean, true)) { $clean[] = $known_row; }
            }

            $rows[$bucket_name] = $clean;
        }

        $order = array('buckets' => $buckets, 'rows' => $rows);

        $path = $this->getDataPath();
        $dir = dirname($path);

        if (!is_dir($dir)) { @mkdir($dir, 0755, true); }

        $php = "<?php\n/* EGESER - Ürün detay \"Teknik Özellikler\" sıralaması.\n"
            . "   Bu dosya admin > Katalog > Teknik Özellik Sırası ekranından\n"
            . "   sürükle-bırak ile güncellenir. Elle düzenlenebilir ama normalde\n"
            . "   buna gerek yok. */\nreturn " . var_export($order, true) . ";\n";

        if (!is_writable($dir) || @file_put_contents($path, $php) === false) {
            $json['error'] = 'Sıralama dosyasına yazılamadı. Sunucuda ' . $dir . ' klasörünün yazma izni olduğundan emin olun.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $json['success'] = true;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
