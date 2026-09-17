<?php
class ModelToolEgeserRedirectManager extends Model {
    public function tableExists() {
        $table = DB_PREFIX . 'egeser_redirect';
        $q = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape($table) . "'");
        return $q->num_rows > 0;
    }

    public function normalizeSource($url) {
        $url = trim(html_entity_decode((string)$url, ENT_QUOTES, 'UTF-8'));
        if ($url === '') return '';

        $parts = @parse_url($url);
        if ($parts === false) return '';
        $path = isset($parts['path']) && $parts['path'] !== '' ? $parts['path'] : '/';
        $path = preg_replace('#/+#', '/', $path);
        if ($path !== '/') $path = rtrim($path, '/');
        if ($path === '') $path = '/';

        $query = array();
        if (!empty($parts['query'])) parse_str($parts['query'], $query);
        $this->ksortRecursive($query);
        $qs = $query ? http_build_query($query, '', '&', PHP_QUERY_RFC3986) : '';
        $qs = str_ireplace('%2F', '/', $qs);
        return $path . ($qs !== '' ? '?' . $qs : '');
    }

    public function normalizeTarget($url) {
        $url = trim((string)$url);
        if ($url === '') return '';
        if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $url) || strpos($url, '//') === 0) return '';
        if ($url[0] !== '/') $url = '/' . $url;
        $parts = @parse_url($url);
        if ($parts === false || isset($parts['host']) || isset($parts['scheme']) || isset($parts['fragment'])) return '';
        $path = isset($parts['path']) ? preg_replace('#/+#', '/', $parts['path']) : '/';
        if (strpos($path, '..') !== false) return '';
        if ($path !== '/') $path = rtrim($path, '/');
        $query = array();
        if (!empty($parts['query'])) parse_str($parts['query'], $query);
        $this->ksortRecursive($query);
        $qs = $query ? http_build_query($query, '', '&', PHP_QUERY_RFC3986) : '';
        $qs = str_ireplace('%2F', '/', $qs);
        return $path . ($qs !== '' ? '?' . $qs : '');
    }

    private function ksortRecursive(&$array) {
        if (!is_array($array)) return;
        ksort($array);
        foreach ($array as &$value) if (is_array($value)) $this->ksortRecursive($value);
    }

    public function hashSource($url) {
        return sha1($this->normalizeSource($url));
    }

    public function getRules() {
        $q = $this->db->query("SELECT * FROM " . DB_PREFIX . "egeser_redirect ORDER BY status DESC, priority ASC, redirect_id ASC");
        return $q->rows;
    }

    public function getRuleByHash($hash) {
        $q = $this->db->query("SELECT * FROM " . DB_PREFIX . "egeser_redirect WHERE source_hash='" . $this->db->escape($hash) . "' LIMIT 1");
        return $q->num_rows ? $q->row : false;
    }

    public function validateRule($data, $current_id = 0, $allow_existing = false) {
        $errors = array();
        $source = $this->normalizeSource(isset($data['source_url']) ? $data['source_url'] : '');
        $target = $this->normalizeTarget(isset($data['target_url']) ? $data['target_url'] : '');
        if ($source === '' || $source === '/') $errors[] = 'Eski URL boş olamaz ve ana sayfa / kaynak olarak kullanılamaz.';
        if ($target === '') $errors[] = 'Hedef yalnızca site içi / ile başlayan bir URL olmalıdır.';
        if ($source !== '' && $target !== '' && $source === $target) $errors[] = 'Kaynak ve hedef aynı olamaz.';
        if ($source !== '') {
            $hash = sha1($source);
            $q = $this->db->query("SELECT redirect_id FROM " . DB_PREFIX . "egeser_redirect WHERE source_hash='" . $this->db->escape($hash) . "'" . ($current_id ? " AND redirect_id != '" . (int)$current_id . "'" : '') . " LIMIT 1");
            if ($q->num_rows && !$allow_existing) $errors[] = 'Bu eski URL için zaten bir kural var.';
        }
        return array('errors'=>$errors,'source_url'=>$source,'target_url'=>$target,'source_hash'=>$source!==''?sha1($source):'');
    }

    public function addRule($data) {
        $v = $this->validateRule($data);
        if ($v['errors']) return array('errors'=>$v['errors']);
        $status = !empty($data['status']) ? 1 : 0;
        $block = !empty($data['block_disabled']) ? 1 : 0;
        if ($status && $this->checkLoop($v['source_url'], $v['target_url'])) return array('errors'=>array('Bu kural aktif edilirse yönlendirme döngüsü oluşur.'));
        $source_label = isset($data['source']) ? utf8_substr(trim($data['source']),0,64) : 'Admin';
        $note = isset($data['note']) ? utf8_substr(trim($data['note']),0,255) : '';
        $priority = isset($data['priority']) ? utf8_substr(trim($data['priority']),0,16) : 'NORMAL';
        $this->db->query("INSERT INTO " . DB_PREFIX . "egeser_redirect SET source_url='".$this->db->escape($v['source_url'])."', source_hash='".$this->db->escape($v['source_hash'])."', target_url='".$this->db->escape($v['target_url'])."', status='".(int)$status."', block_disabled='".(int)$block."', source='".$this->db->escape($source_label)."', note='".$this->db->escape($note)."', priority='".$this->db->escape($priority)."', date_added=NOW(), date_modified=NOW()");
        return array('redirect_id'=>$this->db->getLastId(),'errors'=>array());
    }

    public function upsertRule($data) {
        $source = $this->normalizeSource(isset($data['source_url'])?$data['source_url']:'');
        $target = $this->normalizeTarget(isset($data['target_url'])?$data['target_url']:'');
        if ($source==='' || $source==='/' || $target==='' || $source===$target) return false;
        $hash=sha1($source);
        $status=!empty($data['status'])?1:0; $block=!empty($data['block_disabled'])?1:0;
        if ($status && $this->checkLoop($source, $target)) return false;
        $source_label=isset($data['source'])?utf8_substr(trim($data['source']),0,64):'CSV';
        $note=isset($data['note'])?utf8_substr(trim($data['note']),0,255):'';
        $priority=isset($data['priority'])?utf8_substr(trim($data['priority']),0,16):'NORMAL';
        $q=$this->db->query("SELECT redirect_id FROM ".DB_PREFIX."egeser_redirect WHERE source_hash='".$this->db->escape($hash)."' LIMIT 1");
        if ($q->num_rows) {
            $id=(int)$q->row['redirect_id'];
            $this->db->query("UPDATE ".DB_PREFIX."egeser_redirect SET source_url='".$this->db->escape($source)."', target_url='".$this->db->escape($target)."', status='".$status."', block_disabled='".$block."', source='".$this->db->escape($source_label)."', note='".$this->db->escape($note)."', priority='".$this->db->escape($priority)."', date_modified=NOW() WHERE redirect_id='".$id."'");
            return 'update';
        }
        $this->db->query("INSERT INTO ".DB_PREFIX."egeser_redirect SET source_url='".$this->db->escape($source)."', source_hash='".$this->db->escape($hash)."', target_url='".$this->db->escape($target)."', status='".$status."', block_disabled='".$block."', source='".$this->db->escape($source_label)."', note='".$this->db->escape($note)."', priority='".$this->db->escape($priority)."', date_added=NOW(), date_modified=NOW()");
        return 'create';
    }

    public function toggle($id) {
        $q=$this->db->query("SELECT * FROM ".DB_PREFIX."egeser_redirect WHERE redirect_id='".(int)$id."' LIMIT 1");
        if (!$q->num_rows) return array('ok'=>false,'error'=>'Kural bulunamadı.');
        $row=$q->row;
        if (!(int)$row['status'] && $this->checkLoop($row['source_url'],$row['target_url'])) return array('ok'=>false,'error'=>'Aktifleştirme engellendi: yönlendirme döngüsü oluşur.');
        $new=(int)$row['status']?0:1;
        $this->db->query("UPDATE ".DB_PREFIX."egeser_redirect SET status='".$new."', date_modified=NOW() WHERE redirect_id='".(int)$id."'");
        return array('ok'=>true,'status'=>$new);
    }
    public function deleteRule($id) { $this->db->query("DELETE FROM ".DB_PREFIX."egeser_redirect WHERE redirect_id='".(int)$id."'"); }

    public function checkLoop($source, $target, $max = 20) {
        $source=$this->normalizeSource($source); $target=$this->normalizeTarget($target);
        if ($source==='' || $target==='') return false;
        $seen=array($source=>true); $cur=$target;
        for ($i=0;$i<$max;$i++) {
            if (isset($seen[$cur])) return true;
            $seen[$cur]=true;
            $hash=sha1($this->normalizeSource($cur));
            $q=$this->db->query("SELECT target_url,status FROM ".DB_PREFIX."egeser_redirect WHERE source_hash='".$this->db->escape($hash)."' LIMIT 1");
            if (!$q->num_rows || !(int)$q->row['status']) return false;
            $cur=$this->normalizeTarget($q->row['target_url']);
        }
        return true;
    }
}
