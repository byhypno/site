<?php
/**
 * EGESER OCMOD Test + Full Auditor Helper V2.0
 * Author: EGESER
 * PHP 7.4 compatible.
 */
class EgeserOcmodTest {
    private $db;
    private $config;

    public function __construct($registry) {
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
    }

    public function getModification($modification_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE modification_id = '" . (int)$modification_id . "'");
        return $query->row;
    }

    public function getModifications() {
        $query = $this->db->query("SELECT modification_id, code, name, author, version, status, date_added, xml FROM " . DB_PREFIX . "modification ORDER BY modification_id ASC");
        return $query->rows;
    }

    public function setStatus($modification_id, $status) {
        $this->db->query("UPDATE " . DB_PREFIX . "modification SET status = '" . (int)$status . "' WHERE modification_id = '" . (int)$modification_id . "'");
    }

    public function restoreStatuses($statuses) {
        foreach ((array)$statuses as $id => $status) {
            $this->setStatus((int)$id, (int)$status);
        }
    }

    public function analyzeXml($xml) {
        $result = array(
            'valid_xml' => false,
            'regex_valid' => true,
            'safe_ab' => false,
            'has_catalog' => false,
            'has_admin' => false,
            'has_system' => false,
            'operations' => 0,
            'paths' => array(),
            'critical_targets' => array(),
            'errors' => array()
        );

        if (!trim($xml)) {
            $result['errors'][] = 'XML boş.';
            return $result;
        }

        $old = libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $ok = $dom->loadXML($xml);
        if (!$ok) {
            foreach (libxml_get_errors() as $error) {
                $result['errors'][] = trim($error->message);
            }
            libxml_clear_errors();
            libxml_use_internal_errors($old);
            return $result;
        }
        libxml_clear_errors();
        libxml_use_internal_errors($old);
        $result['valid_xml'] = true;

        $critical = array(
            'admin/controller/extension/modification.php',
            'admin/controller/tool/egeser_ocmod_test.php',
            'system/engine/action.php',
            'system/engine/loader.php',
            'system/framework.php',
            'system/startup.php'
        );

        $files = $dom->getElementsByTagName('file');
        foreach ($files as $file) {
            $path_attr = trim($file->getAttribute('path'));
            foreach (explode('|', $path_attr) as $path) {
                $path = trim($path);
                if ($path === '') continue;
                if (!in_array($path, $result['paths'], true)) $result['paths'][] = $path;
                if (strpos($path, 'catalog/') === 0) $result['has_catalog'] = true;
                if (strpos($path, 'admin/') === 0) $result['has_admin'] = true;
                if (strpos($path, 'system/') === 0) {
                    $result['has_system'] = true;
                    // System katmanı tüm siteyi etkileyebileceği için batch toggle yapılmaz; temiz OCMOD logu üzerinden değerlendirilir.
                    $result['critical_targets'][] = $path;
                }
                foreach ($critical as $needle) {
                    if ($path === $needle || strpos($path, $needle) !== false) {
                        $result['critical_targets'][] = $path;
                        break;
                    }
                }
            }

            foreach ($file->getElementsByTagName('operation') as $operation) {
                $result['operations']++;
                $search = $operation->getElementsByTagName('search')->item(0);
                if ($search && $search->getAttribute('regex') === 'true') {
                    $pattern = trim($search->textContent);
                    if (@preg_match($pattern, 'EGESER_OCMOD_AUDIT') === false) {
                        $result['regex_valid'] = false;
                        $result['errors'][] = 'Geçersiz regex: ' . $this->shorten($pattern, 180);
                    }
                }
                $ignore = $operation->getElementsByTagName('ignoreif')->item(0);
                if ($ignore && $ignore->getAttribute('regex') === 'true') {
                    $pattern = trim($ignore->textContent);
                    if (@preg_match($pattern, 'EGESER_OCMOD_AUDIT') === false) {
                        $result['regex_valid'] = false;
                        $result['errors'][] = 'Geçersiz ignoreif regex: ' . $this->shorten($pattern, 180);
                    }
                }
            }
        }

        $result['critical_targets'] = array_values(array_unique($result['critical_targets']));
        $result['safe_ab'] = $result['valid_xml'] && $result['regex_valid'] && empty($result['critical_targets']);
        if ($result['critical_targets']) $result['errors'][] = 'Denetim altyapısı / çekirdek çalışma dosyasına dokunuyor: otomatik toggle yerine log/statik analiz uygulanır.';
        return $result;
    }

    public function getTestUrls() {
        $base = defined('HTTP_CATALOG') ? HTTP_CATALOG : (defined('HTTPS_CATALOG') ? HTTPS_CATALOG : '/');
        $base = rtrim($base, '/') . '/';
        $urls = array(
            array('label' => 'Ana Sayfa', 'url' => $base),
            array('label' => 'İletişim', 'url' => $base . 'index.php?route=information/contact'),
            array('label' => 'Hakkımızda', 'url' => $base . 'index.php?route=information/information&information_id=7'),
            array('label' => 'Projelerimiz', 'url' => $base . 'index.php?route=information/information&information_id=9'),
            array('label' => 'Teknik Bilgiler', 'url' => $base . 'index.php?route=information/information&information_id=10'),
            array('label' => 'Tüm Modeller', 'url' => $base . 'index.php?route=product/category&path=59'),
            array('label' => 'Tek Katlı', 'url' => $base . 'index.php?route=product/category&path=59_60'),
            array('label' => 'Çift Katlı', 'url' => $base . 'index.php?route=product/category&path=59_61')
        );

        // Representative active categories beyond the known core tree.
        $cq = $this->db->query("SELECT c.category_id FROM " . DB_PREFIX . "category c WHERE c.status='1' ORDER BY c.sort_order ASC, c.category_id ASC LIMIT 20");
        foreach ($cq->rows as $row) {
            $id = (int)$row['category_id'];
            $exists = false;
            foreach ($urls as $u) if (strpos($u['url'], 'path=' . $id) !== false) { $exists = true; break; }
            if (!$exists) $urls[] = array('label' => 'Kategori #' . $id, 'url' => $base . 'index.php?route=product/category&path=' . $id);
        }

        // Sample up to 8 live products: enough to catch product-template, CTA and schema changes.
        $pq = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE status='1' AND date_available <= NOW() ORDER BY sort_order ASC, product_id DESC LIMIT 8");
        $i = 1;
        foreach ($pq->rows as $row) {
            $urls[] = array('label' => 'Ürün Test ' . $i, 'url' => $base . 'index.php?route=product/product&product_id=' . (int)$row['product_id']);
            $i++;
        }
        return $urls;
    }

    public function snapshotAll($urls) {
        $out = array();
        foreach ((array)$urls as $item) {
            $out[] = array('label' => $item['label'], 'url' => $item['url'], 'snapshot' => $this->snapshotUrl($item['url']));
        }
        return $out;
    }

    public function compareAll($baseline, $test) {
        $results = array();
        $count = min(count($baseline), count($test));
        for ($i = 0; $i < $count; $i++) {
            $a = $baseline[$i]['snapshot'];
            $b = $test[$i]['snapshot'];
            $diffs = array();
            $severity = 'same';
            $fields = array(
                'http_code' => 'critical', 'fetch_error' => 'critical', 'php_errors' => 'critical',
                'title' => 'changed', 'meta_description' => 'changed', 'canonical' => 'changed', 'robots' => 'changed',
                'h1_count' => 'critical', 'h1_text' => 'changed', 'h2_hash' => 'changed',
                'forms_hash' => 'changed', 'href_hash' => 'changed', 'internal_href_hash' => 'changed',
                'tel_hash' => 'changed', 'whatsapp_hash' => 'changed', 'mailto_hash' => 'changed',
                'breadcrumb_hash' => 'changed', 'schema_hash' => 'changed', 'asset_hash' => 'changed',
                'image_hash' => 'changed', 'text_hash' => 'changed'
            );
            foreach ($fields as $field => $level) {
                if (isset($a[$field]) && isset($b[$field]) && $a[$field] !== $b[$field]) {
                    $diffs[] = $field;
                    if ($level === 'critical') $severity = 'critical';
                    elseif ($severity === 'same') $severity = 'changed';
                }
            }
            $results[] = array(
                'label' => $baseline[$i]['label'], 'url' => $baseline[$i]['url'],
                'severity' => $severity, 'diffs' => array_values(array_unique($diffs)),
                'before' => $a, 'after' => $b
            );
        }
        return $results;
    }

    public function summarizePageDiffs($comparison) {
        $summary = array('same' => 0, 'changed' => 0, 'critical' => 0, 'changed_pages' => array());
        foreach ((array)$comparison as $row) {
            $sev = isset($row['severity']) ? $row['severity'] : 'same';
            if (!isset($summary[$sev])) $summary[$sev] = 0;
            $summary[$sev]++;
            if ($sev !== 'same') $summary['changed_pages'][] = $row['label'] . ': ' . implode(', ', $row['diffs']);
        }
        return $summary;
    }

    public function fingerprintModificationTree() {
        $map = array();
        if (!defined('DIR_MODIFICATION') || !is_dir(DIR_MODIFICATION)) {
            return array('root_hash' => hash('sha256', ''), 'file_count' => 0, 'files' => $map);
        }
        $base = rtrim(str_replace('\\', '/', DIR_MODIFICATION), '/') . '/';
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(DIR_MODIFICATION, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if (!$file->isFile()) continue;
            $full = str_replace('\\', '/', $file->getPathname());
            $rel = strpos($full, $base) === 0 ? substr($full, strlen($base)) : basename($full);
            if ($rel === 'index.html') continue;
            $map[$rel] = hash_file('sha256', $file->getPathname());
        }
        ksort($map);
        $parts = array();
        foreach ($map as $path => $hash) $parts[] = $path . ':' . $hash;
        return array('root_hash' => hash('sha256', implode("\n", $parts)), 'file_count' => count($map), 'files' => $map);
    }

    public function compareModificationTrees($before, $after) {
        $a = isset($before['files']) ? $before['files'] : array();
        $b = isset($after['files']) ? $after['files'] : array();
        $paths = array_values(array_unique(array_merge(array_keys($a), array_keys($b))));
        sort($paths);
        $changed = array();
        foreach ($paths as $path) {
            $ha = isset($a[$path]) ? $a[$path] : null;
            $hb = isset($b[$path]) ? $b[$path] : null;
            if ($ha !== $hb) $changed[] = $path;
        }
        return array(
            'same' => empty($changed),
            'changed_count' => count($changed),
            'changed_files' => array_slice($changed, 0, 80),
            'before_count' => isset($before['file_count']) ? $before['file_count'] : count($a),
            'after_count' => isset($after['file_count']) ? $after['file_count'] : count($b)
        );
    }

    public function getLatestOcmodLogSection($name) {
        $file = defined('DIR_LOGS') ? DIR_LOGS . 'ocmod.log' : '';
        $out = array('found' => false, 'applied_lines' => 0, 'not_found' => 0, 'files' => array(), 'codes' => 0, 'section' => '');
        if (!$file || !is_file($file)) return $out;
        $log = file_get_contents($file);
        $needle = 'MOD: ' . $name;
        $pos = strrpos($log, $needle);
        if ($pos === false) return $out;
        $section = substr($log, $pos);
        $next = strpos($section, "\nMOD: ", strlen($needle));
        if ($next !== false) $section = substr($section, 0, $next);
        // A timestamped new refresh can also begin before another MOD marker.
        if (preg_match('/\n\d{4}-\d{2}-\d{2}[^\n]* - MOD:/', $section, $m, PREG_OFFSET_CAPTURE, strlen($needle))) {
            $section = substr($section, 0, $m[0][1]);
        }
        $out['found'] = true;
        $out['applied_lines'] = preg_match_all('/^LINE:\s*\d+/m', $section, $m1);
        $out['not_found'] = substr_count($section, 'NOT FOUND - OPERATION SKIPPED!');
        $out['codes'] = preg_match_all('/^(CODE|REGEX):/m', $section, $m2);
        if (preg_match_all('/^FILE:\s*(.+)$/m', $section, $fm)) $out['files'] = array_values(array_unique(array_map('trim', $fm[1])));
        $out['section'] = $this->shorten($section, 4000);
        return $out;
    }

    public function createAuditState($auto_disable) {
        $mods = $this->getModifications();
        $statuses = array();
        $active = array();
        $static = array();
        foreach ($mods as $mod) {
            $id = (int)$mod['modification_id'];
            $statuses[$id] = (int)$mod['status'];
            $analysis = $this->analyzeXml($mod['xml']);
            $static[$id] = array(
                'modification_id' => $id, 'name' => $mod['name'], 'code' => $mod['code'], 'author' => $mod['author'],
                'version' => $mod['version'], 'original_status' => (int)$mod['status'], 'analysis' => $analysis
            );
            if ((int)$mod['status'] === 1) $active[] = $id;
        }
        return array(
            'audit_id' => $this->newTestId(), 'started_at' => date('c'), 'updated_at' => date('c'),
            'auto_disable' => $auto_disable ? 1 : 0, 'original_statuses' => $statuses,
            'queue' => $active, 'index' => 0, 'static' => $static, 'results' => array(),
            'disabled_ids' => array(), 'kept_ids' => array(), 'stage' => 'created',
            'urls' => $this->getTestUrls(), 'final_health' => array()
        );
    }

    public function auditStatePath($audit_id) {
        return DIR_LOGS . 'egeser_ocmod_audit_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $audit_id) . '.json';
    }

    public function saveAuditState($audit_id, $state) {
        $state['updated_at'] = date('c');
        return file_put_contents($this->auditStatePath($audit_id), json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
    }

    public function loadAuditState($audit_id) {
        $file = $this->auditStatePath($audit_id);
        if (!is_file($file)) return array();
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : array();
    }

    public function listAuditFiles() {
        $files = glob(DIR_LOGS . 'egeser_ocmod_audit_*.json');
        if (!$files) return array();
        usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });
        return $files;
    }

    public function auditResultCounts($state) {
        $counts = array('disabled' => 0, 'kept' => 0, 'protected' => 0, 'broken' => 0, 'inert' => 0, 'effective' => 0, 'unknown' => 0);
        foreach ((array)$state['results'] as $row) {
            $decision = isset($row['decision']) ? $row['decision'] : '';
            if (strpos($decision, 'disabled') !== false) $counts['disabled']++;
            if (strpos($decision, 'kept') !== false) $counts['kept']++;
            if (strpos($decision, 'protected') !== false) $counts['protected']++;
            if (strpos($decision, 'broken') !== false) $counts['broken']++;
            if (strpos($decision, 'inert') !== false || strpos($decision, 'no_match') !== false || strpos($decision, 'empty') !== false) $counts['inert']++;
            if (strpos($decision, 'effective') !== false) $counts['effective']++;
            if (strpos($decision, 'unknown') !== false) $counts['unknown']++;
        }
        return $counts;
    }

    /**
     * EGESER OCMOD Final Cleanup V1.0
     * Yalniz kapali ve etkisizligi/bozuklugu kanitli kayitlari aday yapar.
     */
    public function getLatestCompletedAuditState() {
        foreach ($this->listAuditFiles() as $file) {
            $data = json_decode(@file_get_contents($file), true);
            if (is_array($data) && isset($data['stage']) && $data['stage'] === 'complete') {
                return $data;
            }
        }
        return array();
    }

    public function buildCleanupPlan($audit) {
        $plan = array();
        $current = array();
        foreach ($this->getModifications() as $mod) {
            $current[(int)$mod['modification_id']] = $mod;
        }

        $kept = array();
        foreach ((array)(isset($audit['kept_ids']) ? $audit['kept_ids'] : array()) as $id) {
            $kept[(int)$id] = true;
        }

        // 1) Tam denetimin bizzat etkisiz/bozuk bularak kapattigi kayitlar.
        foreach ((array)(isset($audit['results']) ? $audit['results'] : array()) as $id => $row) {
            $id = (int)$id;
            if (!isset($current[$id]) || isset($kept[$id])) continue;
            if ((int)$current[$id]['status'] !== 0) continue;
            $decision = isset($row['decision']) ? (string)$row['decision'] : '';
            $allowed = array('empty_disabled', 'broken_disabled', 'inert_disabled', 'protected_no_match_disabled');
            if (!in_array($decision, $allowed, true)) continue;
            $plan[$id] = array(
                'modification_id' => $id,
                'name' => $current[$id]['name'],
                'version' => $current[$id]['version'],
                'author' => $current[$id]['author'],
                'reason' => isset($row['reason']) ? $row['reason'] : 'Tam denetimde etkisiz/bozuk bulundu.',
                'source' => 'Tam Denetim',
                'decision' => $decision
            );
        }

        // 2) Denetim baslamadan once zaten kapali olan; XML/regex bozuk ya da operasyonu olmayan kayitlar.
        foreach ((array)(isset($audit['static']) ? $audit['static'] : array()) as $id => $row) {
            $id = (int)$id;
            if (isset($plan[$id]) || !isset($current[$id]) || isset($kept[$id])) continue;
            if ((int)$current[$id]['status'] !== 0) continue;
            $analysis = isset($row['analysis']) && is_array($row['analysis']) ? $row['analysis'] : $this->analyzeXml($current[$id]['xml']);
            $reason = '';
            $decision = '';
            if (empty($analysis['valid_xml'])) {
                $reason = 'Geçersiz OCMOD XML; zaten kapalı.';
                $decision = 'static_invalid_xml';
            } elseif (empty($analysis['regex_valid'])) {
                $reason = 'Geçersiz regex; zaten kapalı ve tekrar açılmamalı.';
                $decision = 'static_invalid_regex';
            } elseif ((int)$analysis['operations'] === 0) {
                $reason = 'OCMOD operasyonu yok; zaten kapalı ve çalışma koduna etkisi yok.';
                $decision = 'static_empty';
            }
            if ($reason !== '') {
                $plan[$id] = array(
                    'modification_id' => $id,
                    'name' => $current[$id]['name'],
                    'version' => $current[$id]['version'],
                    'author' => $current[$id]['author'],
                    'reason' => $reason,
                    'source' => 'Statik Denetim',
                    'decision' => $decision
                );
            }
        }

        ksort($plan);
        return array_values($plan);
    }

    public function newCleanupId() {
        return $this->newTestId();
    }

    public function cleanupBackupPath($cleanup_id) {
        return DIR_LOGS . 'egeser_ocmod_cleanup_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $cleanup_id) . '.json';
    }

    public function cleanupLogBackupPath($cleanup_id) {
        return DIR_LOGS . 'egeser_ocmod_cleanup_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $cleanup_id) . '_ocmod.log';
    }

    public function saveCleanupBackup($cleanup_id, $audit_id, $rows) {
        $payload = array(
            'cleanup_id' => $cleanup_id,
            'audit_id' => $audit_id,
            'created_at' => date('c'),
            'rows' => array_values($rows),
            'restored_at' => ''
        );
        $ok = @file_put_contents($this->cleanupBackupPath($cleanup_id), json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
        $log = defined('DIR_LOGS') ? DIR_LOGS . 'ocmod.log' : '';
        if ($log && is_file($log)) @copy($log, $this->cleanupLogBackupPath($cleanup_id));
        return $ok;
    }

    public function loadCleanupBackup($cleanup_id) {
        $file = $this->cleanupBackupPath($cleanup_id);
        if (!is_file($file)) return array();
        $data = json_decode(@file_get_contents($file), true);
        return is_array($data) ? $data : array();
    }

    public function markCleanupRestored($cleanup_id) {
        $data = $this->loadCleanupBackup($cleanup_id);
        if (!$data) return false;
        $data['restored_at'] = date('c');
        return @file_put_contents($this->cleanupBackupPath($cleanup_id), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
    }

    public function getRowsByIds($ids) {
        $out = array();
        foreach ((array)$ids as $id) {
            $id = (int)$id;
            if (!$id) continue;
            $q = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE modification_id='" . $id . "'");
            if ($q->row) $out[] = $q->row;
        }
        return $out;
    }

    public function deleteModificationIds($ids) {
        foreach ((array)$ids as $id) {
            $id = (int)$id;
            if (!$id) continue;
            $this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE modification_id='" . $id . "' AND status='0'");
        }
    }

    public function restoreModificationRows($rows) {
        foreach ((array)$rows as $row) {
            if (!isset($row['modification_id'])) continue;
            $id = (int)$row['modification_id'];
            $exists = $this->db->query("SELECT modification_id FROM " . DB_PREFIX . "modification WHERE modification_id='" . $id . "'");
            if ($exists->row) continue;
            $this->db->query("INSERT INTO " . DB_PREFIX . "modification SET modification_id='" . $id . "', name='" . $this->db->escape(isset($row['name'])?$row['name']:'') . "', code='" . $this->db->escape(isset($row['code'])?$row['code']:'') . "', author='" . $this->db->escape(isset($row['author'])?$row['author']:'') . "', version='" . $this->db->escape(isset($row['version'])?$row['version']:'') . "', link='" . $this->db->escape(isset($row['link'])?$row['link']:'') . "', xml='" . $this->db->escape(isset($row['xml'])?$row['xml']:'') . "', status='" . (int)(isset($row['status'])?$row['status']:0) . "', date_added='" . $this->db->escape(isset($row['date_added'])?$row['date_added']:date('Y-m-d H:i:s')) . "'");
        }
    }

    public function resetOcmodLog() {
        $file = defined('DIR_LOGS') ? DIR_LOGS . 'ocmod.log' : '';
        if (!$file) return false;
        return @file_put_contents($file, '') !== false;
    }

    public function cleanupHealthSummary() {
        $file = defined('DIR_LOGS') ? DIR_LOGS . 'ocmod.log' : '';
        $text = ($file && is_file($file)) ? (string)@file_get_contents($file) : '';
        return array(
            'bytes' => strlen($text),
            'mods' => substr_count($text, 'MOD:'),
            'not_found' => substr_count($text, 'NOT FOUND - OPERATION SKIPPED!'),
            'regex_errors' => substr_count($text, 'No ending delimiter'),
            'warnings' => substr_count($text, 'Warning'),
            'fatal' => substr_count($text, 'Fatal error') + substr_count($text, 'Parse error')
        );
    }

    public function listCleanupBackups() {
        $files = glob(DIR_LOGS . 'egeser_ocmod_cleanup_*.json');
        if (!$files) return array();
        usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });
        $out = array();
        foreach (array_slice($files, 0, 10) as $file) {
            $data = json_decode(@file_get_contents($file), true);
            if (is_array($data)) $out[] = $data;
        }
        return $out;
    }

    public function statePath($test_id) {
        return DIR_LOGS . 'egeser_ocmod_test_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $test_id) . '.json';
    }

    public function saveState($test_id, $state) {
        $state['updated_at'] = date('c');
        return file_put_contents($this->statePath($test_id), json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
    }

    public function loadState($test_id) {
        $file = $this->statePath($test_id);
        if (!is_file($file)) return array();
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : array();
    }

    public function newTestId() {
        if (function_exists('openssl_random_pseudo_bytes')) return bin2hex(openssl_random_pseudo_bytes(8));
        return substr(md5(uniqid('', true)), 0, 16);
    }

    private function snapshotUrl($url) {
        $body = '';
        $code = 0;
        $final_url = $url;
        $redirects = 0;
        $error = '';
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 12);
            curl_setopt($ch, CURLOPT_USERAGENT, 'EGESER-OCMOD-AUDIT/2.0');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $body = curl_exec($ch);
            if ($body === false) { $error = curl_error($ch); $body = ''; }
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $final_url = (string)curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $redirects = (int)curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
            curl_close($ch);
        } else {
            $ctx = stream_context_create(array('http' => array('timeout' => 12, 'ignore_errors' => true, 'user_agent' => 'EGESER-OCMOD-AUDIT/2.0')));
            $body = @file_get_contents($url, false, $ctx);
            if ($body === false) { $body = ''; $error = 'HTTP fetch başarısız'; }
            if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) $code = (int)$m[1];
        }
        $metrics = $this->htmlMetrics($body, $url);
        $metrics['http_code'] = $code;
        $metrics['final_url'] = $final_url;
        $metrics['redirects'] = $redirects;
        $metrics['bytes'] = strlen($body);
        $metrics['fetch_error'] = $error;
        return $metrics;
    }

    private function htmlMetrics($html, $source_url) {
        $emptyHash = hash('sha256', '');
        $result = array(
            'title' => '', 'meta_description' => '', 'canonical' => '', 'robots' => '',
            'h1_count' => 0, 'h1_text' => '', 'h2_hash' => $emptyHash,
            'form_count' => 0, 'forms_hash' => $emptyHash,
            'link_count' => 0, 'href_hash' => $emptyHash, 'internal_href_hash' => $emptyHash,
            'tel_count' => 0, 'tel_hash' => $emptyHash, 'whatsapp_count' => 0, 'whatsapp_hash' => $emptyHash,
            'mailto_hash' => $emptyHash, 'image_count' => 0, 'image_hash' => $emptyHash,
            'breadcrumb_hash' => $emptyHash, 'schema_hash' => $emptyHash, 'asset_hash' => $emptyHash,
            'php_errors' => 0, 'text_hash' => $emptyHash
        );
        if ($html === '') return $result;

        foreach (array('Fatal error', 'Parse error', 'Warning:', 'Notice:', 'Undefined variable', 'Undefined index') as $pattern) $result['php_errors'] += substr_count($html, $pattern);
        $old = libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($old);
        $xp = new DOMXPath($dom);

        $titles = $dom->getElementsByTagName('title');
        if ($titles->length) $result['title'] = $this->cleanText($titles->item(0)->textContent);
        foreach ($xp->query('//meta[@name="description"]') as $n) { $result['meta_description'] = trim($n->getAttribute('content')); break; }
        foreach ($xp->query('//meta[@name="robots"]') as $n) { $result['robots'] = trim($n->getAttribute('content')); break; }
        foreach ($xp->query('//link[contains(concat(" ", normalize-space(@rel), " "), " canonical ")]') as $n) { $result['canonical'] = trim($n->getAttribute('href')); break; }

        $h1s = $dom->getElementsByTagName('h1');
        $result['h1_count'] = $h1s->length;
        $h1txt = array(); foreach ($h1s as $n) $h1txt[] = $this->cleanText($n->textContent); $result['h1_text'] = implode(' | ', $h1txt);
        $h2txt = array(); foreach ($dom->getElementsByTagName('h2') as $n) $h2txt[] = $this->cleanText($n->textContent); $result['h2_hash'] = $this->arrayHash($h2txt);

        $baseHost = parse_url($source_url, PHP_URL_HOST);
        $hrefs = $internal = $tels = $was = $mails = array();
        foreach ($dom->getElementsByTagName('a') as $a) {
            $href = trim($a->getAttribute('href'));
            if ($href === '') continue;
            $norm = $this->normalizeUrlValue($href);
            $hrefs[] = $norm;
            $lower = strtolower($href);
            if (strpos($lower, 'tel:') === 0) $tels[] = $norm;
            if (strpos($lower, 'wa.me/') !== false || strpos($lower, 'api.whatsapp.com/') !== false || strpos($lower, 'whatsapp://') === 0) $was[] = $norm;
            if (strpos($lower, 'mailto:') === 0) $mails[] = $norm;
            $host = parse_url($href, PHP_URL_HOST);
            if ($host === null || $host === false || $host === '' || $host === $baseHost) $internal[] = $norm;
        }
        $result['link_count'] = count($hrefs); $result['href_hash'] = $this->arrayHash($hrefs); $result['internal_href_hash'] = $this->arrayHash($internal);
        $result['tel_count'] = count($tels); $result['tel_hash'] = $this->arrayHash($tels);
        $result['whatsapp_count'] = count($was); $result['whatsapp_hash'] = $this->arrayHash($was); $result['mailto_hash'] = $this->arrayHash($mails);

        $forms = array();
        foreach ($dom->getElementsByTagName('form') as $f) {
            $inputs = array();
            foreach ($f->getElementsByTagName('input') as $in) {
                $name = trim($in->getAttribute('name')); $type = strtolower(trim($in->getAttribute('type')));
                if ($name !== '') $inputs[] = $type . ':' . $name;
            }
            sort($inputs);
            $forms[] = strtoupper(trim($f->getAttribute('method'))) . '|' . $this->normalizeUrlValue(trim($f->getAttribute('action'))) . '|' . implode(',', $inputs);
        }
        $result['form_count'] = count($forms); $result['forms_hash'] = $this->arrayHash($forms);

        $images = array(); foreach ($dom->getElementsByTagName('img') as $n) $images[] = $this->normalizeUrlValue(trim($n->getAttribute('src')));
        $result['image_count'] = count($images); $result['image_hash'] = $this->arrayHash($images);

        $crumb = array(); foreach ($xp->query('//*[contains(concat(" ", normalize-space(@class), " "), " breadcrumb ")]//a') as $n) $crumb[] = $this->cleanText($n->textContent) . '|' . $this->normalizeUrlValue($n->getAttribute('href'));
        $result['breadcrumb_hash'] = $this->arrayHash($crumb);

        $schemas = array(); foreach ($xp->query('//script[@type="application/ld+json"]') as $n) $schemas[] = preg_replace('/\s+/u', ' ', trim($n->textContent));
        $result['schema_hash'] = $this->arrayHash($schemas);

        $assets = array();
        foreach ($xp->query('//script[@src]') as $n) $assets[] = 'js:' . $this->normalizeUrlValue($n->getAttribute('src'));
        foreach ($xp->query('//link[@href]') as $n) { $rel = strtolower($n->getAttribute('rel')); if (strpos($rel, 'stylesheet') !== false) $assets[] = 'css:' . $this->normalizeUrlValue($n->getAttribute('href')); }
        $result['asset_hash'] = $this->arrayHash($assets);

        $bodies = $dom->getElementsByTagName('body');
        $text = $bodies->length ? $bodies->item(0)->textContent : strip_tags($html);
        $result['text_hash'] = hash('sha256', $this->cleanText($text));
        return $result;
    }

    private function normalizeUrlValue($value) {
        $value = html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8');
        // Remove volatile session/token values without hiding route/path differences.
        $value = preg_replace('/([?&](?:token|sid|PHPSESSID|_)=)[^&#]*/i', '$1{volatile}', $value);
        return trim($value);
    }

    private function arrayHash($items) {
        $items = array_values(array_filter(array_map('strval', (array)$items), function($v) { return $v !== ''; }));
        sort($items, SORT_STRING);
        return hash('sha256', implode("\n", $items));
    }

    private function cleanText($text) {
        $text = html_entity_decode((string)$text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }

    private function shorten($text, $limit) {
        $text = (string)$text;
        if (function_exists('mb_strlen') && mb_strlen($text, 'UTF-8') > $limit) return mb_substr($text, 0, $limit, 'UTF-8') . '…';
        if (strlen($text) > $limit) return substr($text, 0, $limit) . '…';
        return $text;
    }
}
