<?php
class ControllerExtensionModuleEgeserLeadCsrf extends Controller {
    public function token() {
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->response->addHeader('Pragma: no-cache');

        if (empty($this->session->data['egeser_csrf_token'])) {
            $this->session->data['egeser_csrf_token'] = $this->createToken();
        }

        $this->response->setOutput(json_encode(array(
            'success' => true,
            'csrf_token' => (string)$this->session->data['egeser_csrf_token']
        )));
    }

    private function createToken() {
        if (function_exists('random_bytes')) {
            try {
                return bin2hex(random_bytes(24));
            } catch (Exception $e) {
                // PHP/OpenSSL fallback below.
            }
        }

        return sha1(uniqid(mt_rand(), true)) . sha1(uniqid(mt_rand(), true));
    }
}
