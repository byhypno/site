<?php
class ControllerExtensionModuleEgeserHealth extends Controller {
    public function ping() {
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate');
        $this->response->setOutput(json_encode(array('ok' => true)));
    }
}
