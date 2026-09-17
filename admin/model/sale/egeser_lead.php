<?php
class ModelSaleEgeserLead extends Model {
    public function ensureSchema() {
        $this->load->model('extension/module/egeser_lead_manager');
        $this->model_extension_module_egeser_lead_manager->install();
    }

    public function getLeads($data = array()) {
        $this->ensureSchema();

        $sql = "SELECT * FROM `" . DB_PREFIX . "egeser_lead_log` WHERE 1";

        if (!empty($data['filter_name'])) {
            $sql .= " AND (`name` LIKE '%" . $this->db->escape($data['filter_name']) . "%' OR `company` LIKE '%" . $this->db->escape($data['filter_name']) . "%')";
        }
        if (!empty($data['filter_phone'])) {
            $sql .= " AND `phone` LIKE '%" . $this->db->escape($data['filter_phone']) . "%'";
        }
        if (!empty($data['filter_location'])) {
            $sql .= " AND `location` LIKE '%" . $this->db->escape($data['filter_location']) . "%'";
        }
        if (!empty($data['filter_status'])) {
            $sql .= " AND `lead_status`='" . $this->db->escape($data['filter_status']) . "'";
        }
        if (!empty($data['filter_customer_type'])) {
            $sql .= " AND `customer_type`='" . $this->db->escape($data['filter_customer_type']) . "'";
        }
        if (!empty($data['filter_project_type'])) {
            $sql .= " AND `project_type` LIKE '%" . $this->db->escape($data['filter_project_type']) . "%'";
        }
        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(`date_added`) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(`date_added`) <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }

        $allowed_sort = array('lead_id','date_added','name','customer_type','project_type','location','area','lead_status','mail_status');
        $sort = isset($data['sort']) && in_array($data['sort'], $allowed_sort) ? $data['sort'] : 'lead_id';
        $order = isset($data['order']) && strtoupper($data['order']) === 'ASC' ? 'ASC' : 'DESC';

        $sql .= " ORDER BY `" . $sort . "` " . $order;

        if (isset($data['start']) || isset($data['limit'])) {
            $start = max(0, (int)$data['start']);
            $limit = isset($data['limit']) ? max(1, min(200, (int)$data['limit'])) : 25;
            $sql .= " LIMIT " . $start . "," . $limit;
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalLeads($data = array()) {
        $this->ensureSchema();
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "egeser_lead_log` WHERE 1";

        if (!empty($data['filter_name'])) {
            $sql .= " AND (`name` LIKE '%" . $this->db->escape($data['filter_name']) . "%' OR `company` LIKE '%" . $this->db->escape($data['filter_name']) . "%')";
        }
        if (!empty($data['filter_phone'])) $sql .= " AND `phone` LIKE '%" . $this->db->escape($data['filter_phone']) . "%'";
        if (!empty($data['filter_location'])) $sql .= " AND `location` LIKE '%" . $this->db->escape($data['filter_location']) . "%'";
        if (!empty($data['filter_status'])) $sql .= " AND `lead_status`='" . $this->db->escape($data['filter_status']) . "'";
        if (!empty($data['filter_customer_type'])) $sql .= " AND `customer_type`='" . $this->db->escape($data['filter_customer_type']) . "'";
        if (!empty($data['filter_project_type'])) $sql .= " AND `project_type` LIKE '%" . $this->db->escape($data['filter_project_type']) . "%'";
        if (!empty($data['filter_date_from'])) $sql .= " AND DATE(`date_added`) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        if (!empty($data['filter_date_to'])) $sql .= " AND DATE(`date_added`) <= '" . $this->db->escape($data['filter_date_to']) . "'";

        return (int)$this->db->query($sql)->row['total'];
    }

    public function getLead($lead_id) {
        $this->ensureSchema();
        $q = $this->db->query("SELECT * FROM `" . DB_PREFIX . "egeser_lead_log` WHERE `lead_id`=" . (int)$lead_id . " LIMIT 1");
        return $q->num_rows ? $q->row : array();
    }

    public function updateLead($lead_id, $data) {
        $status_options = array('Yeni','Arandı','Teklif Verildi','Satış','İptal');
        $status = in_array($data['lead_status'], $status_options) ? $data['lead_status'] : 'Yeni';

        $this->db->query("UPDATE `" . DB_PREFIX . "egeser_lead_log`
            SET `lead_status`='" . $this->db->escape($status) . "',
                `assigned_to`='" . $this->db->escape(utf8_substr($data['assigned_to'],0,100)) . "',
                `admin_note`='" . $this->db->escape($data['admin_note']) . "',
                `date_modified`=NOW()
            WHERE `lead_id`=" . (int)$lead_id);
    }

    public function deleteLead($lead_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_lead_log` WHERE `lead_id`=" . (int)$lead_id);
    }
}
