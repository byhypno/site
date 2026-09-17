<?php
require_once(DIR_SYSTEM . 'library/egeser_health_monitor.php');
require_once(DIR_SYSTEM . 'library/egeser_performance_monitor.php');
require_once(DIR_SYSTEM . 'library/egeser_security_monitor.php');

class ModelExtensionModuleEgeserHealth extends Model {
    private function monitor() {
        return new EgeserHealthMonitor($this->registry);
    }

    public function migrateTablesToInnoDB() {
        $tables = array(
            'egeser_lead_log','egeser_404_log','egeser_health_run',
            'egeser_link_issue','egeser_performance_run','egeser_search_spam_log',
            'egeser_rate_limit','egeser_redirect_audit','egeser_lead_rate_limit'
        );
        foreach ($tables as $table) {
            $q = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape(DB_PREFIX . $table) . "'");
            if ($q->num_rows) {
                try {
                    $this->db->query("ALTER TABLE `" . DB_PREFIX . $table . "` ENGINE=InnoDB");
                } catch (Exception $e) {
                    $this->log->write('Egeser V10 InnoDB migration skipped for ' . $table . ': ' . $e->getMessage());
                }
            }
        }
    }

    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_lead_log` (
            `lead_id` INT(11) NOT NULL AUTO_INCREMENT,
            `customer_type` VARCHAR(30) NOT NULL,
            `company` VARCHAR(150) NOT NULL DEFAULT '',
            `name` VARCHAR(80) NOT NULL,
            `phone` VARCHAR(30) NOT NULL,
            `email` VARCHAR(120) NOT NULL DEFAULT '',
            `project_type` VARCHAR(100) NOT NULL DEFAULT '',
            `location` VARCHAR(120) NOT NULL DEFAULT '',
            `area` VARCHAR(40) NOT NULL DEFAULT '',
            `product_id` INT(11) NOT NULL DEFAULT 0,
            `product_name` VARCHAR(255) NOT NULL DEFAULT '',
            `message` TEXT NOT NULL,
            `source` VARCHAR(255) NOT NULL DEFAULT '',
            `page_url` VARCHAR(700) NOT NULL DEFAULT '',
            `utm_source` VARCHAR(100) NOT NULL DEFAULT '',
            `utm_medium` VARCHAR(100) NOT NULL DEFAULT '',
            `utm_campaign` VARCHAR(150) NOT NULL DEFAULT '',
            `mail_status` VARCHAR(20) NOT NULL DEFAULT 'pending',
            `mail_error` VARCHAR(500) NOT NULL DEFAULT '',
            `consent` TINYINT(1) NOT NULL DEFAULT 0,
            `date_added` DATETIME NOT NULL,
            PRIMARY KEY (`lead_id`),
            KEY `idx_date_added` (`date_added`),
            KEY `idx_mail_status` (`mail_status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        $this->ensureLeadColumns();

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_404_log` (
            `log_id` INT(11) NOT NULL AUTO_INCREMENT,
            `url` VARCHAR(500) NOT NULL,
            `referrer` VARCHAR(500) NOT NULL DEFAULT '',
            `hits` INT(11) NOT NULL DEFAULT 1,
            `last_seen` DATETIME NOT NULL,
            PRIMARY KEY (`log_id`),
            UNIQUE KEY `url_unique` (`url`(190))
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        $this->monitor()->installTables();
        $this->migrateTablesToInnoDB();
        $performance = new EgeserPerformanceMonitor($this->registry);
        $performance->installTables();
        $security = new EgeserSecurityMonitor($this->registry);
        $security->installTables();
    }


    private function ensureLeadColumns() {
        if (!$this->tableExists('egeser_lead_log')) return;

        $columns = array(
            'location' => "VARCHAR(120) NOT NULL DEFAULT ''",
            'area' => "VARCHAR(40) NOT NULL DEFAULT ''",
            'product_id' => "INT(11) NOT NULL DEFAULT 0",
            'product_name' => "VARCHAR(255) NOT NULL DEFAULT ''",
            'message' => "TEXT NOT NULL",
            'page_url' => "VARCHAR(700) NOT NULL DEFAULT ''",
            'utm_medium' => "VARCHAR(100) NOT NULL DEFAULT ''",
            'utm_campaign' => "VARCHAR(150) NOT NULL DEFAULT ''"
        );

        foreach ($columns as $column => $definition) {
            $q = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "egeser_lead_log` LIKE '" . $this->db->escape($column) . "'");
            if (!$q->num_rows) {
                $this->db->query("ALTER TABLE `" . DB_PREFIX . "egeser_lead_log` ADD `" . $column . "` " . $definition);
            }
        }
    }

    private function tableExists($table) {
        $query = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape(DB_PREFIX . $table) . "'");
        return (bool)$query->num_rows;
    }

    public function getReport() {
        return $this->monitor()->getReport();
    }

    public function runHealth($with_scan = false, $max_pages = 40, $run_type = 'manual') {
        return $this->monitor()->run($run_type, $with_scan, $max_pages);
    }

    public function getRecentRuns($limit = 10) {
        return $this->monitor()->getRecentRuns($limit);
    }

    public function getOpenIssues($limit = 25) {
        return $this->monitor()->getOpenIssues($limit);
    }

    public function getLeadStats() {
        if (!$this->tableExists('egeser_lead_log')) {
            return array('today'=>0,'week'=>0,'failed'=>0,'corporate'=>0,'individual'=>0);
        }
        $q = $this->db->query("SELECT
            COALESCE(SUM(date_added >= CURDATE()),0) AS today,
            COALESCE(SUM(date_added >= DATE_SUB(NOW(), INTERVAL 7 DAY)),0) AS week,
            COALESCE(SUM(mail_status='failed' AND date_added >= DATE_SUB(NOW(), INTERVAL 7 DAY)),0) AS failed,
            COALESCE(SUM(customer_type='Kurumsal' AND date_added >= DATE_SUB(NOW(), INTERVAL 30 DAY)),0) AS corporate,
            COALESCE(SUM(customer_type='Bireysel' AND date_added >= DATE_SUB(NOW(), INTERVAL 30 DAY)),0) AS individual
            FROM `" . DB_PREFIX . "egeser_lead_log`");
        return array_map('intval', $q->row);
    }

    public function getRecentLeads($limit = 10) {
        if (!$this->tableExists('egeser_lead_log')) return array();
        $limit = max(1, min(50, (int)$limit));
        $q = $this->db->query("SELECT lead_id,customer_type,company,name,phone,email,project_type,location,area,product_id,product_name,source,mail_status,date_added
            FROM `" . DB_PREFIX . "egeser_lead_log` ORDER BY lead_id DESC LIMIT " . $limit);
        foreach ($q->rows as &$row) {
            $row['phone_masked'] = $this->mask($row['phone'], 4);
            $row['email_masked'] = $row['email'] ? $this->maskEmail($row['email']) : '-';
        }
        unset($row);
        return $q->rows;
    }

    private function mask($value, $show = 4) {
        $len = utf8_strlen($value);
        if ($len <= $show) return str_repeat('*', $len);
        return str_repeat('*', $len - $show) . utf8_substr($value, -$show);
    }

    private function maskEmail($email) {
        $parts = explode('@', $email, 2);
        if (count($parts) !== 2) return '***';
        return utf8_substr($parts[0], 0, 1) . '***@' . $parts[1];
    }


    public function installPerformance() {
        $monitor = new EgeserPerformanceMonitor($this->registry);
        $monitor->installTables();
    }

    public function runPerformance($run_type = 'manual') {
        $monitor = new EgeserPerformanceMonitor($this->registry);
        return $monitor->run($run_type);
    }

    public function getRecentPerformanceRuns($limit = 10) {
        $monitor = new EgeserPerformanceMonitor($this->registry);
        return $monitor->getRecentRuns($limit);
    }

    public function getLatestPerformance() {
        $monitor = new EgeserPerformanceMonitor($this->registry);
        return $monitor->getLatest();
    }


    public function getSecurityStats() {
        $monitor = new EgeserSecurityMonitor($this->registry);
        return $monitor->getSpamStats();
    }

    public function getRecentSpam($limit = 25) {
        $monitor = new EgeserSecurityMonitor($this->registry);
        return $monitor->getRecentSpam($limit);
    }

    public function auditRedirect($url) {
        $monitor = new EgeserSecurityMonitor($this->registry);
        return $monitor->auditRedirect($url, 8);
    }

    public function getRecentRedirectAudits($limit = 20) {
        $monitor = new EgeserSecurityMonitor($this->registry);
        return $monitor->getRecentRedirectAudits($limit);
    }

    public function cleanup($days) {
        $days = max(30, min(730, (int)$days));
        if ($this->tableExists('egeser_lead_log')) {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_lead_log`
                WHERE date_added < DATE_SUB(NOW(), INTERVAL " . $days . " DAY)");
        }
        if ($this->tableExists('egeser_404_log')) {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_404_log`
                WHERE last_seen < DATE_SUB(NOW(), INTERVAL 180 DAY)");
        }
        $this->monitor()->cleanup($days);
        $performance = new EgeserPerformanceMonitor($this->registry);
        $performance->cleanup($days);
        $security = new EgeserSecurityMonitor($this->registry);
        $security->cleanup($days);
    }
}
