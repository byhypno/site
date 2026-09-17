<?php
require_once(DIR_SYSTEM . 'library/egeser_health_monitor.php');
require_once(DIR_SYSTEM . 'library/egeser_performance_monitor.php');

class ModelExtensionModuleEgeserHealthCron extends Model {
    public function run($max_pages) {
        $monitor = new EgeserHealthMonitor($this->registry);
        $days = (int)$this->config->get('egeser_health_retention_days');
        if ($days < 30) $days = 90;
        $monitor->cleanup($days);
        $health = $monitor->run('cron', true, $max_pages);
        $performance = new EgeserPerformanceMonitor($this->registry);
        $performance->cleanup($days);
        $perf = $this->config->get('egeser_performance_cron_status') ? $performance->run('cron') : array();
        return array('health'=>$health, 'performance'=>$perf);
    }
}
