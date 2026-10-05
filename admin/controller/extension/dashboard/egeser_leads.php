<?php
class ControllerExtensionDashboardEgeserLeads extends Controller {
	private $error = array();
	private $permission_key = 'extension/dashboard/egeser_leads';

	public function index() {
		$this->load->language('extension/dashboard/egeser_leads');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_egeser_leads', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true));
		$data['breadcrumbs'][] = array('text' => $this->language->get('text_extension'), 'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true));
		$data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/dashboard/egeser_leads', 'token=' . $this->session->data['token'], true));

		$data['action'] = $this->url->link('extension/dashboard/egeser_leads', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true);

		$data['dashboard_egeser_leads_width'] = isset($this->request->post['dashboard_egeser_leads_width']) ? $this->request->post['dashboard_egeser_leads_width'] : $this->config->get('dashboard_egeser_leads_width');
		$data['dashboard_egeser_leads_status'] = isset($this->request->post['dashboard_egeser_leads_status']) ? $this->request->post['dashboard_egeser_leads_status'] : $this->config->get('dashboard_egeser_leads_status');
		$data['dashboard_egeser_leads_sort_order'] = isset($this->request->post['dashboard_egeser_leads_sort_order']) ? $this->request->post['dashboard_egeser_leads_sort_order'] : $this->config->get('dashboard_egeser_leads_sort_order');

		$data['columns'] = array();
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard/egeser_leads_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->permission_key)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function dashboard() {
		$this->load->language('extension/dashboard/egeser_leads');
		$this->load->model('sale/egeser_lead');

		$leads = $this->model_sale_egeser_lead->getLeads(array('sort' => 'date_added', 'order' => 'DESC', 'limit' => 6));

		$today = date('Y-m-d');
		$week_start = date('Y-m-d', strtotime('-6 days'));

		$today_count = $this->model_sale_egeser_lead->getTotalLeads(array('filter_date_from' => $today, 'filter_date_to' => $today));
		$week_count = $this->model_sale_egeser_lead->getTotalLeads(array('filter_date_from' => $week_start, 'filter_date_to' => $today));

		$status_class = array('Satış' => 'label-success', 'İptal' => 'label-default', 'Yeni' => 'label-danger');

		foreach ($leads as &$lead) {
			$lead['status_class'] = isset($status_class[$lead['lead_status']]) ? $status_class[$lead['lead_status']] : 'label-info';
			$lead['view_url'] = $this->url->link('sale/egeser_lead/view', 'token=' . $this->session->data['token'] . '&lead_id=' . (int)$lead['lead_id'], true);
			$lead['time_ago'] = $this->timeAgo($lead['date_added']);
		}
		unset($lead);

		$data['heading_title'] = $this->language->get('heading_title');
		$data['leads'] = $leads;
		$data['today_count'] = (int)$today_count;
		$data['week_count'] = (int)$week_count;
		$data['list_url'] = $this->url->link('sale/egeser_lead', 'token=' . $this->session->data['token'], true);

		return $this->load->view('extension/dashboard/egeser_leads_info', $data);
	}

	private function timeAgo($datetime) {
		$diff = time() - strtotime($datetime);

		if ($diff < 60) return 'az önce';
		if ($diff < 3600) return floor($diff / 60) . ' dakika önce';
		if ($diff < 86400) return floor($diff / 3600) . ' saat önce';
		if ($diff < 172800) return 'dün';

		return floor($diff / 86400) . ' gün önce';
	}

	public function install() {
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', $this->permission_key);
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', $this->permission_key);

		// OpenCart 2.3'un extension/extension/dashboard.php'si oc_extension'a
		// 'dashboard_' onekli kod yazar ama getInstalled()/getList() oneksiz
		// kodu bekler (cekirdek hatasi - widget hicbir zaman "Kurulu"
		// gorunmez). Dogru (oneksiz) kaydi burada kendimiz ekliyoruz.
		$this->load->model('extension/extension');
		$this->model_extension_extension->install('dashboard', 'egeser_leads');

		require_once(DIR_SYSTEM . 'library/egeser_lead_schema.php');
		EgeserLeadSchema::install($this->db);
	}

	public function uninstall() {
		$this->load->model('extension/extension');
		$this->model_extension_extension->uninstall('dashboard', 'egeser_leads');
	}
}
