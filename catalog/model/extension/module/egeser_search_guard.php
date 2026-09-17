<?php
require_once(DIR_SYSTEM . 'library/egeser_security_monitor.php');

class ModelExtensionModuleEgeserSearchGuard extends Model {
    private function monitor() {
        return new EgeserSecurityMonitor($this->registry);
    }

    public function inspect($query) {
        $monitor = $this->monitor();
        $monitor->installTables();

        $suspicious = $monitor->isSuspiciousSearch($query);
        if ($suspicious) $monitor->logSuspiciousSearch($query);

        $limit = (int)$this->config->get('egeser_security_search_rate_limit');
        if ($limit < 5) $limit = 30;

        $allowed = $monitor->rateLimit('search', $limit, 60);

        return array(
            'suspicious'=>$suspicious,
            'allowed'=>$allowed
        );
    }
}
