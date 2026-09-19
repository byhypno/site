<?php
class ControllerExtensionModuleEgeserLeadSecurity extends Controller {
    public function nonce() {
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->response->addHeader('Pragma: no-cache');

        if ($this->request->server['REQUEST_METHOD'] !== 'GET') {
            $this->response->addHeader('HTTP/1.1 405 Method Not Allowed');
            $this->response->setOutput(json_encode(array('success'=>false,'message'=>'Geçersiz istek.')));
            return;
        }

        $xhr = isset($this->request->server['HTTP_X_REQUESTED_WITH']) ? strtolower((string)$this->request->server['HTTP_X_REQUESTED_WITH']) : '';
        if ($xhr !== 'xmlhttprequest') {
            $this->response->addHeader('HTTP/1.1 403 Forbidden');
            $this->response->setOutput(json_encode(array('success'=>false,'message'=>'Geçersiz istek kaynağı.')));
            return;
        }

        if (!(bool)$this->config->get('egeser_lead_security_status')) {
            $this->response->addHeader('HTTP/1.1 503 Service Unavailable');
            $this->response->setOutput(json_encode(array('success'=>false,'message'=>'Form güvenliği devre dışı.')));
            return;
        }

        $this->load->model('extension/module/egeser_lead_security');
        $token = $this->model_extension_module_egeser_lead_security->issueNonce();

        $this->response->setOutput(json_encode(array(
            'success'=>true,
            'csrf_token'=>$token
        )));
    }
}
