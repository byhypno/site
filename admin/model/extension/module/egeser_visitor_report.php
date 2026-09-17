<?php
require_once(DIR_SYSTEM . 'library/egeser_visitor_schema.php');

class ModelExtensionModuleEgeserVisitorReport extends Model {
    private $page_events = "'page_view','product_view','category_view'";

    public function install() {
        EgeserVisitorSchema::install($this->db);
    }

    // ------------------------------------------------------------------
    // Dashboard / Gunluk Rapor KPI'lari
    // ------------------------------------------------------------------

    public function getKpis($date_from, $date_to) {
        $range = $this->range($date_from, $date_to);

        $visitors = (int)$this->db->query("SELECT COUNT(DISTINCT visitor_id) AS c FROM `" . DB_PREFIX . "egeser_session`
            WHERE started_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'")->row['c'];

        $sessions = (int)$this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_session`
            WHERE started_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'")->row['c'];

        $pageviews = $this->countEvents($this->page_events, $range);
        $product_views = $this->countEvents("'product_view'", $range);
        $whatsapp = $this->countEvents("'whatsapp_click'", $range);
        $phone = $this->countEvents("'phone_click'", $range);
        $quote = $this->countEvents("'quote_form_submit'", $range);
        $contact = $this->countEvents("'contact_form_submit'", $range);
        $conversions = $quote + $contact;

        return array(
            'visitors' => $visitors,
            'sessions' => $sessions,
            'pageviews' => $pageviews,
            'product_views' => $product_views,
            'whatsapp_clicks' => $whatsapp,
            'phone_clicks' => $phone,
            'quote_forms' => $quote,
            'contact_forms' => $contact,
            'conversions' => $conversions,
            'conversion_rate' => $sessions > 0 ? round(($conversions / $sessions) * 100, 1) : 0
        );
    }

    private function countEvents($type_sql, $range) {
        $q = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_event`
            WHERE event_type IN (" . $type_sql . ")
              AND created_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'");
        return (int)$q->row['c'];
    }

    private function range($date_from, $date_to) {
        return array($date_from . ' 00:00:00', $date_to . ' 23:59:59');
    }

    // ------------------------------------------------------------------
    // Grafikler
    // ------------------------------------------------------------------

    public function getHourlyTraffic($date_from, $date_to) {
        $range = $this->range($date_from, $date_to);

        $q = $this->db->query("SELECT HOUR(created_at) AS hour, COUNT(*) AS total
            FROM `" . DB_PREFIX . "egeser_event`
            WHERE event_type IN (" . $this->page_events . ")
              AND created_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'
            GROUP BY HOUR(created_at)");

        $hours = array_fill(0, 24, 0);
        foreach ($q->rows as $row) {
            $hours[(int)$row['hour']] = (int)$row['total'];
        }

        return $hours;
    }

    public function getSourceDistribution($date_from, $date_to) {
        $range = $this->range($date_from, $date_to);

        $q = $this->db->query("SELECT source, COUNT(*) AS total FROM `" . DB_PREFIX . "egeser_session`
            WHERE started_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'
            GROUP BY source ORDER BY total DESC");

        return $q->rows;
    }

    public function getDeviceDistribution($date_from, $date_to) {
        $range = $this->range($date_from, $date_to);

        $q = $this->db->query("SELECT device_type, COUNT(*) AS total FROM `" . DB_PREFIX . "egeser_session`
            WHERE started_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'
            GROUP BY device_type ORDER BY total DESC");

        return $q->rows;
    }

    public function getTopProductsShort($date_from, $date_to, $limit = 5) {
        $range = $this->range($date_from, $date_to);

        $q = $this->db->query("SELECT e.entity_id, COUNT(*) AS views, COUNT(DISTINCT e.visitor_id) AS visitors, p.model,
                pd.name
            FROM `" . DB_PREFIX . "egeser_event` e
            LEFT JOIN `" . DB_PREFIX . "product` p ON (p.product_id = e.entity_id)
            LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (pd.product_id = e.entity_id AND pd.language_id = " . (int)$this->config->get('config_language_id') . ")
            WHERE e.event_type = 'product_view' AND e.entity_id > 0
              AND e.created_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'
            GROUP BY e.entity_id ORDER BY views DESC LIMIT " . (int)$limit);

        return $q->rows;
    }

    public function getTopSourcesShort($date_from, $date_to, $limit = 5) {
        $range = $this->range($date_from, $date_to);

        $q = $this->db->query("SELECT source, COUNT(*) AS sessions FROM `" . DB_PREFIX . "egeser_session`
            WHERE started_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'
            GROUP BY source ORDER BY sessions DESC LIMIT " . (int)$limit);

        return $q->rows;
    }

    public function getRecentConversions($limit = 10) {
        $q = $this->db->query("SELECT c.*, v.visitor_token
            FROM `" . DB_PREFIX . "egeser_conversion` c
            LEFT JOIN `" . DB_PREFIX . "egeser_visitor` v ON (v.visitor_id = c.visitor_id)
            ORDER BY c.created_at DESC LIMIT " . (int)$limit);

        return $q->rows;
    }

    // ------------------------------------------------------------------
    // Canli Ziyaretciler
    // ------------------------------------------------------------------

    public function getLiveVisitors() {
        $q = $this->db->query("SELECT s.session_id, s.visitor_id, s.source, s.medium, s.landing_page,
                s.device_type, s.pageviews, s.started_at, s.last_activity, s.converted,
                v.visitor_token
            FROM `" . DB_PREFIX . "egeser_session` s
            LEFT JOIN `" . DB_PREFIX . "egeser_visitor` v ON (v.visitor_id = s.visitor_id)
            WHERE s.last_activity >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
            ORDER BY s.last_activity DESC");

        $sessions = $q->rows;
        if (!$sessions) {
            return array();
        }

        $ids = array();
        foreach ($sessions as $row) {
            $ids[] = (int)$row['session_id'];
        }
        $id_list = implode(',', $ids);

        $last_page = array();
        $lp = $this->db->query("SELECT e1.session_id, e1.page_title, e1.page_url, e1.route
            FROM `" . DB_PREFIX . "egeser_event` e1
            INNER JOIN (
                SELECT session_id, MAX(event_id) AS max_id FROM `" . DB_PREFIX . "egeser_event`
                WHERE session_id IN (" . $id_list . ") AND event_type IN (" . $this->page_events . ")
                GROUP BY session_id
            ) latest ON (latest.session_id = e1.session_id AND latest.max_id = e1.event_id)");
        foreach ($lp->rows as $row) {
            $last_page[(int)$row['session_id']] = $row;
        }

        $whatsapp_sessions = array();
        $wa = $this->db->query("SELECT DISTINCT session_id FROM `" . DB_PREFIX . "egeser_event`
            WHERE session_id IN (" . $id_list . ") AND event_type = 'whatsapp_click'");
        foreach ($wa->rows as $row) {
            $whatsapp_sessions[(int)$row['session_id']] = true;
        }

        $quote_sessions = array();
        $qs = $this->db->query("SELECT DISTINCT session_id FROM `" . DB_PREFIX . "egeser_event`
            WHERE session_id IN (" . $id_list . ") AND event_type = 'quote_form_start'");
        foreach ($qs->rows as $row) {
            $quote_sessions[(int)$row['session_id']] = true;
        }

        foreach ($sessions as &$row) {
            $sid = (int)$row['session_id'];
            $row['current_page'] = isset($last_page[$sid]) ? $last_page[$sid]['page_title'] : '';
            $row['current_url'] = isset($last_page[$sid]) ? $last_page[$sid]['page_url'] : '';
            $row['whatsapp_clicked'] = isset($whatsapp_sessions[$sid]);
            $row['quote_started'] = isset($quote_sessions[$sid]) || (bool)$row['converted'];
        }

        return $sessions;
    }

    // ------------------------------------------------------------------
    // Trafik Kaynaklari (detayli)
    // ------------------------------------------------------------------

    public function getTrafficSources($date_from, $date_to) {
        $range = $this->range($date_from, $date_to);

        $q = $this->db->query("SELECT
                s.source,
                COUNT(*) AS sessions,
                COUNT(DISTINCT s.visitor_id) AS visitors,
                SUM(s.pageviews) AS pageviews,
                SUM(s.converted) AS conversions
            FROM `" . DB_PREFIX . "egeser_session` s
            WHERE s.started_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'
            GROUP BY s.source ORDER BY sessions DESC");

        $rows = $q->rows;

        foreach ($rows as &$row) {
            $row['product_views'] = 0;
            $row['whatsapp_clicks'] = 0;

            $pv = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_event` e
                INNER JOIN `" . DB_PREFIX . "egeser_session` s2 ON (s2.session_id = e.session_id)
                WHERE s2.source='" . $this->db->escape($row['source']) . "'
                  AND e.event_type='product_view'
                  AND s2.started_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'");
            $row['product_views'] = (int)$pv->row['c'];

            $wa = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_event` e
                INNER JOIN `" . DB_PREFIX . "egeser_session` s2 ON (s2.session_id = e.session_id)
                WHERE s2.source='" . $this->db->escape($row['source']) . "'
                  AND e.event_type='whatsapp_click'
                  AND s2.started_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'");
            $row['whatsapp_clicks'] = (int)$wa->row['c'];

            $row['conversion_rate'] = $row['sessions'] > 0 ? round(((int)$row['conversions'] / (int)$row['sessions']) * 100, 1) : 0;
        }

        return $rows;
    }

    // ------------------------------------------------------------------
    // Urun Raporlari
    // ------------------------------------------------------------------

    public function getProductReport($date_from, $date_to, $start = 0, $limit = 25) {
        $range = $this->range($date_from, $date_to);

        $q = $this->db->query("SELECT
                e.entity_id AS product_id,
                pd.name,
                COUNT(*) AS views,
                COUNT(DISTINCT e.visitor_id) AS visitors
            FROM `" . DB_PREFIX . "egeser_event` e
            LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (pd.product_id = e.entity_id AND pd.language_id = " . (int)$this->config->get('config_language_id') . ")
            WHERE e.event_type = 'product_view' AND e.entity_id > 0
              AND e.created_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'
            GROUP BY e.entity_id
            ORDER BY views DESC
            LIMIT " . (int)$start . "," . (int)$limit);

        $rows = $q->rows;

        foreach ($rows as &$row) {
            $pid = (int)$row['product_id'];

            $wa = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_event`
                WHERE event_type='whatsapp_click' AND entity_type='product' AND entity_id=" . $pid . "
                  AND created_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'");
            $row['whatsapp_clicks'] = (int)$wa->row['c'];

            $tel = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_event`
                WHERE event_type='phone_click' AND entity_type='product' AND entity_id=" . $pid . "
                  AND created_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'");
            $row['phone_clicks'] = (int)$tel->row['c'];

            $qf = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_event`
                WHERE event_type='quote_form_submit' AND entity_type='product' AND entity_id=" . $pid . "
                  AND created_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'");
            $row['quote_forms'] = (int)$qf->row['c'];

            $row['conversion_rate'] = $row['visitors'] > 0 ? round(($row['quote_forms'] / $row['visitors']) * 100, 1) : 0;
        }

        return $rows;
    }

    public function getTotalProducts($date_from, $date_to) {
        $range = $this->range($date_from, $date_to);

        $q = $this->db->query("SELECT COUNT(DISTINCT entity_id) AS c FROM `" . DB_PREFIX . "egeser_event`
            WHERE event_type = 'product_view' AND entity_id > 0
              AND created_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'");

        return (int)$q->row['c'];
    }

    // ------------------------------------------------------------------
    // Ziyaretci Yolculuklari
    // ------------------------------------------------------------------

    public function getVisitorJourneys($date_from, $date_to, $start = 0, $limit = 25, $only_converted = false) {
        $range = $this->range($date_from, $date_to);

        $sql = "SELECT s.session_id, s.visitor_id, s.source, s.medium, s.landing_page, s.device_type,
                s.pageviews, s.started_at, s.last_activity, s.converted, s.lead_id, v.visitor_token
            FROM `" . DB_PREFIX . "egeser_session` s
            LEFT JOIN `" . DB_PREFIX . "egeser_visitor` v ON (v.visitor_id = s.visitor_id)
            WHERE s.started_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'";

        if ($only_converted) {
            $sql .= " AND s.converted = 1";
        }

        $sql .= " ORDER BY s.started_at DESC LIMIT " . (int)$start . "," . (int)$limit;

        return $this->db->query($sql)->rows;
    }

    public function getTotalVisitorJourneys($date_from, $date_to, $only_converted = false) {
        $range = $this->range($date_from, $date_to);

        $sql = "SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_session`
            WHERE started_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'";

        if ($only_converted) {
            $sql .= " AND converted = 1";
        }

        return (int)$this->db->query($sql)->row['c'];
    }

    public function getSessionTimeline($session_id) {
        $q = $this->db->query("SELECT * FROM `" . DB_PREFIX . "egeser_event`
            WHERE session_id=" . (int)$session_id . " ORDER BY created_at ASC, event_id ASC");

        return $q->rows;
    }

    public function getSession($session_id) {
        $q = $this->db->query("SELECT s.*, v.visitor_token, v.first_seen, v.first_source, v.first_medium
            FROM `" . DB_PREFIX . "egeser_session` s
            LEFT JOIN `" . DB_PREFIX . "egeser_visitor` v ON (v.visitor_id = s.visitor_id)
            WHERE s.session_id=" . (int)$session_id . " LIMIT 1");

        return $q->num_rows ? $q->row : array();
    }

    public function getVisitorSessionCount($visitor_id) {
        $q = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_session` WHERE visitor_id=" . (int)$visitor_id);
        return (int)$q->row['c'];
    }

    /**
     * Lead ile eslesen oturumu bulur (admin Lead Manager ekraninda
     * "Musteri Yolculugu" gostermek icin).
     */
    public function getSessionByLeadId($lead_id) {
        $q = $this->db->query("SELECT s.*, v.visitor_token, v.first_seen, v.first_source, v.first_medium, v.first_landing_page
            FROM `" . DB_PREFIX . "egeser_session` s
            LEFT JOIN `" . DB_PREFIX . "egeser_visitor` v ON (v.visitor_id = s.visitor_id)
            WHERE s.lead_id=" . (int)$lead_id . " LIMIT 1");

        return $q->num_rows ? $q->row : array();
    }

    // ------------------------------------------------------------------
    // Donusumler
    // ------------------------------------------------------------------

    public function getConversions($date_from, $date_to, $start = 0, $limit = 25) {
        $range = $this->range($date_from, $date_to);

        $q = $this->db->query("SELECT c.*, v.visitor_token
            FROM `" . DB_PREFIX . "egeser_conversion` c
            LEFT JOIN `" . DB_PREFIX . "egeser_visitor` v ON (v.visitor_id = c.visitor_id)
            WHERE c.created_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'
            ORDER BY c.created_at DESC LIMIT " . (int)$start . "," . (int)$limit);

        return $q->rows;
    }

    public function getTotalConversions($date_from, $date_to) {
        $range = $this->range($date_from, $date_to);

        $q = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_conversion`
            WHERE created_at BETWEEN '" . $this->db->escape($range[0]) . "' AND '" . $this->db->escape($range[1]) . "'");

        return (int)$q->row['c'];
    }

    // ------------------------------------------------------------------
    // Ayarlar / veri saklama (retention)
    // ------------------------------------------------------------------

    public function cleanup($event_days, $session_days, $visitor_days) {
        $result = array('events_deleted' => 0, 'sessions_deleted' => 0, 'visitors_deleted' => 0);

        do {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_event`
                WHERE created_at < DATE_SUB(NOW(), INTERVAL " . (int)$event_days . " DAY) LIMIT 2000");
            $affected = $this->db->countAffected();
            $result['events_deleted'] += $affected;
        } while ($affected > 0);

        do {
            $this->db->query("DELETE s FROM `" . DB_PREFIX . "egeser_session` s
                WHERE s.last_activity < DATE_SUB(NOW(), INTERVAL " . (int)$session_days . " DAY)
                  AND NOT EXISTS (SELECT 1 FROM `" . DB_PREFIX . "egeser_event` e WHERE e.session_id = s.session_id)
                LIMIT 2000");
            $affected = $this->db->countAffected();
            $result['sessions_deleted'] += $affected;
        } while ($affected > 0);

        do {
            $this->db->query("DELETE v FROM `" . DB_PREFIX . "egeser_visitor` v
                WHERE v.last_seen < DATE_SUB(NOW(), INTERVAL " . (int)$visitor_days . " DAY)
                  AND NOT EXISTS (SELECT 1 FROM `" . DB_PREFIX . "egeser_session` s WHERE s.visitor_id = v.visitor_id)
                LIMIT 2000");
            $affected = $this->db->countAffected();
            $result['visitors_deleted'] += $affected;
        } while ($affected > 0);

        return $result;
    }
}
