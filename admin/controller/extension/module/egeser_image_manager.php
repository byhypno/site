<?php
class ControllerExtensionModuleEgeserImageManager extends Controller {
    private $error = array();

    private function slots() {
        return array(
            'home_tek_katli' => array(
                'label' => 'Anasayfa — Tek Katlı Görsel',
                'help'  => 'Anasayfada "Tek Katlı" seçeneği için gösterilen fotoğraf.',
                'path'  => 'catalog/egeser/home/tek-katli-placeholder.jpg'
            ),
            'home_cift_katli' => array(
                'label' => 'Anasayfa — Çift Katlı Görsel',
                'help'  => 'Anasayfada "Çift Katlı" seçeneği için gösterilen fotoğraf.',
                'path'  => 'catalog/egeser/home/cift-katli-placeholder.jpg'
            ),
            'hakkimizda_showroom' => array(
                'label' => 'Hakkımızda — Showroom Fotoğrafı',
                'help'  => 'Hakkımızda sayfasındaki galeri bölümünün büyük fotoğrafı.',
                'path'  => 'catalog/egeser/hakkimizda/showroom.jpg'
            ),
            'hakkimizda_uretim' => array(
                'label' => 'Hakkımızda — Üretim Fotoğrafı',
                'help'  => 'Hakkımızda sayfasındaki galeri bölümünün üretim fotoğrafı.',
                'path'  => 'catalog/egeser/hakkimizda/uretim.jpg'
            ),
            'hakkimizda_ofis' => array(
                'label' => 'Hakkımızda — Ofis Fotoğrafı',
                'help'  => 'Hakkımızda sayfasındaki galeri bölümünün ofis fotoğrafı.',
                'path'  => 'catalog/egeser/hakkimizda/ofis.jpg'
            )
        );
    }

    public function index() {
        $this->load->language('extension/module/egeser_image_manager');
        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $slots = $this->slots();
            $key = (string)$this->request->post['slot'];

            $target = DIR_IMAGE . $slots[$key]['path'];
            $target_dir = dirname($target);

            if (!is_dir($target_dir)) {
                @mkdir($target_dir, 0755, true);
            }

            move_uploaded_file($this->request->files['image']['tmp_name'], $target);
            @chmod($target, 0644);

            $this->session->data['success'] = 'Görsel güncellendi: ' . $slots[$key]['label'];
            $this->response->redirect($this->url->link('extension/module/egeser_image_manager', 'token=' . $this->session->data['token'], true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_intro'] = $this->language->get('text_intro');

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => 'Ana Sayfa',
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => 'Modüller',
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/egeser_image_manager', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/module/egeser_image_manager', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        $data['slots'] = array();

        foreach ($this->slots() as $key => $slot) {
            $file = DIR_IMAGE . $slot['path'];

            $data['slots'][] = array(
                'key'    => $key,
                'label'  => $slot['label'],
                'help'   => $slot['help'],
                'exists' => is_file($file),
                'image'  => is_file($file) ? (HTTP_CATALOG . 'image/' . $slot['path'] . '?v=' . filemtime($file)) : ''
            );
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/egeser_image_manager', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_image_manager')) {
            $this->error['warning'] = 'Bu modülü değiştirme yetkiniz yok.';
            return false;
        }

        $slots = $this->slots();
        $key = isset($this->request->post['slot']) ? (string)$this->request->post['slot'] : '';

        if (!isset($slots[$key])) {
            $this->error['warning'] = 'Geçersiz görsel alanı.';
            return false;
        }

        if (empty($this->request->files['image']) || !is_uploaded_file($this->request->files['image']['tmp_name'])) {
            $this->error['warning'] = 'Lütfen bir görsel dosyası seçin.';
            return false;
        }

        if ($this->request->files['image']['error'] !== UPLOAD_ERR_OK) {
            $this->error['warning'] = 'Dosya yüklenirken bir hata oluştu.';
            return false;
        }

        if ($this->request->files['image']['size'] > 5 * 1024 * 1024) {
            $this->error['warning'] = 'Dosya boyutu 5 MB üzerinde olamaz.';
            return false;
        }

        $extension = strtolower(pathinfo($this->request->files['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, array('jpg', 'jpeg', 'png', 'webp'), true)) {
            $this->error['warning'] = 'Sadece JPG, PNG veya WEBP dosyaları yüklenebilir.';
            return false;
        }

        $info = @getimagesize($this->request->files['image']['tmp_name']);

        if (!$info) {
            $this->error['warning'] = 'Seçilen dosya geçerli bir görsel değil.';
            return false;
        }

        return true;
    }

    public function install() {
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/egeser_image_manager');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/egeser_image_manager');
    }

    public function uninstall() {
        // İçeriği bilinçli olarak silmez.
    }
}
