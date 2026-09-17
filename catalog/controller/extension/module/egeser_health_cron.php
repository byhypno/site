<?php
class ControllerExtensionModuleEgeserHealthCron extends Controller {
    private function secureEquals($known, $user) {
        if (function_exists('hash_equals')) return hash_equals($known, $user);
        if (!is_string($known) || !is_string($user) || strlen($known) !== strlen($user)) return false;
        $result = 0;
        for ($i = 0, $length = strlen($known); $i < $length; $i++) {
            $result |= ord($known[$i]) ^ ord($user[$i]);
        }
        return $result === 0;
    }

    public function index() {
        $this->response->addHeader('X-Robots-Tag: noindex, nofollow');
        $this->response->addHeader('X-Content-Type-Options: nosniff');
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate');

        if (!$this->config->get('egeser_health_status')) {
            $this->response->setOutput(json_encode(array('error'=>'Site Kontrol Merkezi kapalı.')));
            return;
        }

        $stored = (string)$this->config->get('egeser_health_cron_key');
        $provided = '';

        if (!empty($this->request->server['HTTP_X_EGESER_CRON_KEY'])) {
            $provided = (string)$this->request->server['HTTP_X_EGESER_CRON_KEY'];
        } elseif (isset($this->request->get['key'])) {
            // URL anahtarı sadece uyumluluk için desteklenir; header kullanımı önerilir.
            $provided = (string)$this->request->get['key'];
        }

        if (strlen($stored) < 32 || !$this->secureEquals($stored, $provided)) {
            $this->response->addHeader('HTTP/1.1 403 Forbidden');
            $this->response->setOutput(json_encode(array('error'=>'Yetkisiz cron isteği.')));
            return;
        }

        $max_pages = (int)$this->config->get('egeser_health_scan_max_pages');
        if ($max_pages < 5) $max_pages = 40;
        $max_pages = min(100, $max_pages);

        $this->load->model('extension/module/egeser_health_cron');

        try {
            $result = $this->model_extension_module_egeser_health_cron->run($max_pages);
            $health = isset($result['health']) ? $result['health'] : array();
            $performance = isset($result['performance']) ? $result['performance'] : array();
            $this->response->setOutput(json_encode(array(
                'success'=>true,
                'score'=>isset($health['score']) ? $health['score'] : 0,
                'status'=>isset($health['status']) ? $health['status'] : 'red',
                'scan'=>isset($health['scan']) ? $health['scan'] : array(),
                'performance'=>$performance
            )));
        } catch (Exception $e) {
            $this->log->write('Egeser Health Cron Error: ' . $e->getMessage());
            $this->response->addHeader('HTTP/1.1 500 Internal Server Error');
            $this->response->setOutput(json_encode(array('error'=>'Sağlık kontrolü tamamlanamadı. Hata günlüğünü kontrol edin.')));
        }
    }
}
