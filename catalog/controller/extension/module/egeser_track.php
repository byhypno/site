<?php
require_once(DIR_SYSTEM . 'library/egeser_visitor_tracker.php');
require_once(DIR_SYSTEM . 'library/egeser_security_monitor.php');

/**
 * Egeser Ziyaretci & Lead Takip Merkezi - frontend beacon endpoint.
 * theme.js icindeki mevcut track() fonksiyonu, whatsapp_click/phone_click/
 * quote_form_submit gibi olaylari buraya POST eder. Sayfa goruntuleme
 * (page_view) burada degil, common/header controller'inda kaydedilir.
 */
class ControllerExtensionModuleEgeserTrack extends Controller {
    public function event() {
        $this->response->addHeader('X-Robots-Tag: noindex, nofollow');
        $this->response->addHeader('X-Content-Type-Options: nosniff');
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate');

        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $this->response->addHeader('HTTP/1.1 405 Method Not Allowed');
            $this->response->setOutput(json_encode(array('success' => false)));
            return;
        }

        try {
            $security = new EgeserSecurityMonitor($this->registry);
            if (!$security->rateLimit('egeser_track_event', 60, 60)) {
                $this->response->addHeader('HTTP/1.1 429 Too Many Requests');
                $this->response->setOutput(json_encode(array('success' => false)));
                return;
            }

            $event_type = isset($this->request->post['event_type']) ? (string)$this->request->post['event_type'] : '';
            $placement = isset($this->request->post['placement']) ? (string)$this->request->post['placement'] : '';
            $page_url = isset($this->request->post['page_url']) ? (string)$this->request->post['page_url'] : '';
            $entity_type = isset($this->request->post['entity_type']) ? (string)$this->request->post['entity_type'] : '';
            $entity_id = isset($this->request->post['entity_id']) ? (int)$this->request->post['entity_id'] : 0;

            $tracker = new EgeserVisitorTracker($this->registry);
            $ok = $tracker->trackClientEvent($event_type, array(
                'placement' => $placement,
                'page_url' => $page_url,
                'entity_type' => $entity_type,
                'entity_id' => $entity_id
            ));

            $this->response->setOutput(json_encode(array('success' => (bool)$ok)));
        } catch (Exception $e) {
            $this->log->write('Egeser Track Event error: ' . $e->getMessage());
            $this->response->setOutput(json_encode(array('success' => false)));
        }
    }
}
