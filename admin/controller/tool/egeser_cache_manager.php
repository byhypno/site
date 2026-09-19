<?php
class ControllerToolEgeserCacheManager extends Controller {
    private $error = array();
    private $log_file = 'egeser_cache_manager.log';

    public function index() {
        $this->load->language('tool/egeser_cache_manager');
        $this->document->setTitle($this->language->get('heading_title'));

        if (!$this->canAccess()) {
            $this->session->data['error_warning'] = $this->language->get('error_permission');
            $this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true));
            return;
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_dashboard'] = $this->language->get('text_dashboard');
        $data['text_intro'] = $this->language->get('text_intro');
        $data['text_safety'] = $this->language->get('text_safety');
        $data['text_ocmod_note'] = $this->language->get('text_ocmod_note');
        $data['text_no_journal'] = $this->language->get('text_no_journal');
        $data['button_clear_cache'] = $this->language->get('button_clear_cache');
        $data['button_clear_all_safe'] = $this->language->get('button_clear_all_safe');
        $data['button_reset_opcache'] = $this->language->get('button_reset_opcache');
        $data['button_refresh_ocmod'] = $this->language->get('button_refresh_ocmod');
        $data['button_refresh'] = $this->language->get('button_refresh');

        $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
        $data['error_warning'] = isset($this->session->data['error_warning']) ? $this->session->data['error_warning'] : '';
        unset($this->session->data['success'], $this->session->data['error_warning']);

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_tools'),
            'href' => $this->url->link('tool/egeser_cache_manager', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('tool/egeser_cache_manager', 'token=' . $this->session->data['token'], true)
        );

        $data['clear_cache_url'] = $this->url->link('tool/egeser_cache_manager/clearCache', 'token=' . $this->session->data['token'], true);
        $data['clear_all_safe_url'] = $this->url->link('tool/egeser_cache_manager/clearAllSafe', 'token=' . $this->session->data['token'], true);
        $data['reset_opcache_url'] = $this->url->link('tool/egeser_cache_manager/resetOpcache', 'token=' . $this->session->data['token'], true);
        $data['ocmod_refresh_url'] = $this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'], true);
        $data['refresh_url'] = $this->url->link('tool/egeser_cache_manager', 'token=' . $this->session->data['token'], true);

        $data['cache'] = $this->getOpenCartCacheStats();
        $data['modification'] = $this->getDirectoryStats(defined('DIR_MODIFICATION') ? DIR_MODIFICATION : '');
        $data['opcache'] = $this->getOpcacheStatus();
        $data['journal_detected'] = $this->detectJournal();
        $data['logs'] = $this->getRecentLogLines(20);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/egeser_cache_manager.tpl', $data));
    }

    public function clearCache() {
        $this->guardModify();
        if ($this->response->getOutput()) return;

        $result = $this->clearOpenCartCache();
        if ($result['success']) {
            $this->writeLog('OpenCart cache temizlendi', $result);
            $this->session->data['success'] = sprintf($this->language->get('text_cache_cleared'), $result['files'], $this->formatBytes($result['bytes']));
        } else {
            $this->session->data['error_warning'] = $result['message'];
        }

        $this->response->redirect($this->url->link('tool/egeser_cache_manager', 'token=' . $this->session->data['token'], true));
    }

    public function clearAllSafe() {
        $this->guardModify();
        if ($this->response->getOutput()) return;

        $cache = $this->clearOpenCartCache();
        $opcache = array('success' => false, 'available' => false);
        if (function_exists('opcache_reset')) {
            $opcache['available'] = true;
            $opcache['success'] = (bool)@opcache_reset();
        }

        if ($cache['success']) {
            $this->writeLog('Güvenli cache temizliği', array(
                'files' => $cache['files'],
                'bytes' => $cache['bytes'],
                'opcache' => ($opcache['available'] ? ($opcache['success'] ? 'reset' : 'reset_failed') : 'unavailable')
            ));
            $this->session->data['success'] = sprintf($this->language->get('text_all_safe_cleared'), $cache['files'], $this->formatBytes($cache['bytes']));
        } else {
            $this->session->data['error_warning'] = $cache['message'];
        }

        $this->response->redirect($this->url->link('tool/egeser_cache_manager', 'token=' . $this->session->data['token'], true));
    }

    public function resetOpcache() {
        $this->guardModify();
        if ($this->response->getOutput()) return;

        if (!function_exists('opcache_reset')) {
            $this->session->data['error_warning'] = $this->language->get('error_opcache_unavailable');
        } else {
            $ok = (bool)@opcache_reset();
            if ($ok) {
                $this->writeLog('PHP OPcache sifirlandi', array());
                $this->session->data['success'] = $this->language->get('text_opcache_reset');
            } else {
                $this->session->data['error_warning'] = $this->language->get('error_opcache_reset');
            }
        }

        $this->response->redirect($this->url->link('tool/egeser_cache_manager', 'token=' . $this->session->data['token'], true));
    }

    private function guardModify() {
        $this->load->language('tool/egeser_cache_manager');
        if (!$this->canModify()) {
            $this->session->data['error_warning'] = $this->language->get('error_permission');
            $this->response->redirect($this->url->link('tool/egeser_cache_manager', 'token=' . $this->session->data['token'], true));
        }
    }

    private function canAccess() {
        return $this->user->hasPermission('access', 'extension/modification') || $this->user->hasPermission('access', 'tool/log');
    }

    private function canModify() {
        return $this->user->hasPermission('modify', 'extension/modification');
    }

    private function getOpenCartCacheStats() {
        $stats = array(
            'path' => defined('DIR_CACHE') ? DIR_CACHE : '',
            'files' => 0,
            'bytes' => 0,
            'size' => '0 B',
            'other_files' => array(),
            'writable' => false
        );

        if (!defined('DIR_CACHE') || !is_dir(DIR_CACHE)) return $stats;
        $stats['writable'] = is_writable(DIR_CACHE);

        $files = glob(DIR_CACHE . 'cache.*');
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    $stats['files']++;
                    $stats['bytes'] += @filesize($file);
                }
            }
        }
        $stats['size'] = $this->formatBytes($stats['bytes']);

        $all = glob(DIR_CACHE . '*');
        if ($all) {
            foreach ($all as $file) {
                if (!is_file($file)) continue;
                $name = basename($file);
                if ($name === 'index.html' || strpos($name, 'cache.') === 0) continue;
                $stats['other_files'][] = array('name' => $name, 'size' => $this->formatBytes(@filesize($file)));
            }
        }

        return $stats;
    }

    private function clearOpenCartCache() {
        $result = array('success' => true, 'files' => 0, 'bytes' => 0, 'message' => '');

        if (!defined('DIR_CACHE') || !is_dir(DIR_CACHE)) {
            $result['success'] = false;
            $result['message'] = $this->language->get('error_cache_path');
            return $result;
        }

        $cache_real = realpath(DIR_CACHE);
        $system_real = defined('DIR_SYSTEM') ? realpath(DIR_SYSTEM) : false;
        if (!$cache_real || !$system_real || strpos(str_replace('\\','/',$cache_real), rtrim(str_replace('\\','/',$system_real),'/') . '/') !== 0) {
            $result['success'] = false;
            $result['message'] = $this->language->get('error_cache_path');
            return $result;
        }

        $files = glob(DIR_CACHE . 'cache.*');
        if (!$files) return $result;

        foreach ($files as $file) {
            if (!is_file($file)) continue;
            $size = @filesize($file);
            if (@unlink($file)) {
                $result['files']++;
                $result['bytes'] += $size;
            } else {
                $result['success'] = false;
                $result['message'] = sprintf($this->language->get('error_delete_file'), basename($file));
                break;
            }
        }

        return $result;
    }

    private function getDirectoryStats($path) {
        $stats = array('path' => $path, 'files' => 0, 'bytes' => 0, 'size' => '0 B', 'exists' => false);
        if (!$path || !is_dir($path)) return $stats;
        $stats['exists'] = true;
        try {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
            foreach ($it as $file) {
                if ($file->isFile() && $file->getFilename() !== 'index.html') {
                    $stats['files']++;
                    $stats['bytes'] += $file->getSize();
                }
            }
        } catch (Exception $e) {}
        $stats['size'] = $this->formatBytes($stats['bytes']);
        return $stats;
    }

    private function getOpcacheStatus() {
        $data = array('available' => false, 'enabled' => false, 'memory' => '', 'scripts' => 0);
        if (!function_exists('opcache_get_status')) return $data;
        $data['available'] = true;
        $status = @opcache_get_status(false);
        if (!is_array($status)) return $data;
        $data['enabled'] = !empty($status['opcache_enabled']);
        if (isset($status['memory_usage']['used_memory'])) {
            $data['memory'] = $this->formatBytes($status['memory_usage']['used_memory']);
        }
        if (isset($status['opcache_statistics']['num_cached_scripts'])) {
            $data['scripts'] = (int)$status['opcache_statistics']['num_cached_scripts'];
        }
        return $data;
    }

    private function detectJournal() {
        $paths = array();
        if (defined('DIR_SYSTEM')) {
            $paths[] = DIR_SYSTEM . 'journal3';
            $paths[] = DIR_SYSTEM . 'library/journal3';
        }
        if (defined('DIR_CATALOG')) {
            $paths[] = DIR_CATALOG . 'view/theme/journal3';
            $paths[] = DIR_CATALOG . 'controller/journal3';
        }
        foreach ($paths as $path) {
            if (is_dir($path)) return true;
        }
        return false;
    }

    private function writeLog($action, $extra) {
        if (!defined('DIR_LOGS') || !is_dir(DIR_LOGS)) return;
        $user = method_exists($this->user, 'getUserName') ? $this->user->getUserName() : ('user_id=' . $this->user->getId());
        $ip = isset($this->request->server['REMOTE_ADDR']) ? $this->request->server['REMOTE_ADDR'] : '-';
        $line = date('Y-m-d H:i:s') . ' | user=' . $this->safeLog($user) . ' | ip=' . $this->safeLog($ip) . ' | action=' . $this->safeLog($action);
        foreach ((array)$extra as $k => $v) {
            if (is_scalar($v)) $line .= ' | ' . $this->safeLog($k) . '=' . $this->safeLog($v);
        }
        @file_put_contents(DIR_LOGS . $this->log_file, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private function getRecentLogLines($limit) {
        $result = array();
        if (!defined('DIR_LOGS')) return $result;
        $file = DIR_LOGS . $this->log_file;
        if (!is_file($file)) return $result;
        $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) return $result;
        return array_reverse(array_slice($lines, -1 * (int)$limit));
    }

    private function safeLog($value) {
        return str_replace(array("\r", "\n", '|'), array(' ', ' ', '/'), (string)$value);
    }

    private function formatBytes($bytes) {
        $bytes = (float)$bytes;
        $units = array('B','KB','MB','GB','TB');
        if ($bytes <= 0) return '0 B';
        $pow = floor(log($bytes, 1024));
        $pow = min($pow, count($units)-1);
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }
}
