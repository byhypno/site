<?php
class EgeserRouteResolver {
    public static function definitions() {
        return array(
            'prefabrik_yapilar' => array('type'=>'category','slug'=>'prefabrik-yapilar'),
            'tek_katli' => array('type'=>'category','slug'=>'tek-katli-prefabrik-evler'),
            'cift_katli' => array('type'=>'category','slug'=>'cift-katli-prefabrik-evler'),
            'ofis_yonetim' => array('type'=>'category','slug'=>'prefabrik-ofis-ve-yonetim-binalari'),
            'yatakhane' => array('type'=>'category','slug'=>'prefabrik-yatakhane-binalari'),
            'yemekhane' => array('type'=>'category','slug'=>'prefabrik-yemekhane-binalari'),
            'santiye' => array('type'=>'category','slug'=>'prefabrik-santiye-yapilari'),
            'sosyal_tesis' => array('type'=>'category','slug'=>'prefabrik-sosyal-tesis-yapilari'),
            'ozel_proje' => array('type'=>'category','slug'=>'ozel-proje-prefabrik-yapilar'),
            'referanslar' => array('type'=>'information','slug'=>'referanslar'),
            'e_katalog' => array('type'=>'information','slug'=>'e-katalog'),
            'kurumsal' => array('type'=>'information','slug'=>'kurumsal'),
            'hakkimizda' => array('type'=>'information','slug'=>'hakkimizda'),
            'teknik' => array('type'=>'information','slug'=>'teknik-bilgiler'),
            'iletisim' => array('type'=>'contact','slug'=>'iletisim')
        );
    }

    public static function resolveAll($db, $url) {
        $result = array();
        foreach (self::definitions() as $key => $def) {
            $result[$key] = self::resolve($db, $url, $def);
        }
        return $result;
    }

    public static function resolve($db, $url, $def) {
        if ($def['type'] === 'contact') {
            return $url->link('information/contact', '', true);
        }

        $slug = isset($def['slug']) ? trim($def['slug']) : '';
        if ($slug === '') return '';

        $q = $db->query("SELECT `query` FROM `" . DB_PREFIX . "url_alias` WHERE `keyword` = '" . $db->escape($slug) . "' LIMIT 1");
        if ($q->num_rows) {
            $query = (string)$q->row['query'];
            if (preg_match('/^category_id=(\\d+)$/', $query, $m)) {
                return $url->link('product/category', 'path=' . (int)$m[1], true);
            }
            if (preg_match('/^information_id=(\\d+)$/', $query, $m)) {
                return $url->link('information/information', 'information_id=' . (int)$m[1], true);
            }
        }

        // SEO URL henüz yaratılmadıysa kontrollü slug fallback'i.
        // İskelet kurulunca bu dal kullanılmaz.
        $base = defined('HTTPS_SERVER') ? HTTPS_SERVER : (defined('HTTP_SERVER') ? HTTP_SERVER : '/');
        return rtrim($base, '/') . '/' . ltrim($slug, '/');
    }
}
