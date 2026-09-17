<?php
class ControllerInformationProject extends Controller {
    public function index() {
        // Mevcut admin panelinde proje detay veri modeli doğrulanmadan ikinci bir proje sistemi oluşturulmaz.
        // Kullanıcıyı mevcut Referanslar arşivine yönlendir.
        $base = $this->config->get('config_ssl') ? $this->config->get('config_ssl') : $this->config->get('config_url');
        $this->response->redirect(rtrim($base, '/') . '/referanslar', 302);
    }
}
