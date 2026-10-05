<?php
require_once(DIR_SYSTEM . 'library/egeser_geo_il.php');

class ControllerExtensionDashboardEgeserMap extends Controller {
	private $error = array();
	private $permission_key = 'extension/dashboard/egeser_map';

	public function index() {
		$this->load->language('extension/dashboard/egeser_map');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_egeser_map', $this->request->post);

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
		$data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/dashboard/egeser_map', 'token=' . $this->session->data['token'], true));

		$data['action'] = $this->url->link('extension/dashboard/egeser_map', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true);

		$data['dashboard_egeser_map_width'] = isset($this->request->post['dashboard_egeser_map_width']) ? $this->request->post['dashboard_egeser_map_width'] : $this->config->get('dashboard_egeser_map_width');
		$data['dashboard_egeser_map_status'] = isset($this->request->post['dashboard_egeser_map_status']) ? $this->request->post['dashboard_egeser_map_status'] : $this->config->get('dashboard_egeser_map_status');
		$data['dashboard_egeser_map_sort_order'] = isset($this->request->post['dashboard_egeser_map_sort_order']) ? $this->request->post['dashboard_egeser_map_sort_order'] : $this->config->get('dashboard_egeser_map_sort_order');

		$data['columns'] = array();
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard/egeser_map_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->permission_key)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function dashboard() {
		$this->load->language('extension/dashboard/egeser_map');

		$this->load->model('extension/module/egeser_visitor_report');
		$this->model_extension_module_egeser_visitor_report->install();

		$date_to = date('Y-m-d');
		$date_from = date('Y-m-d', strtotime('-29 days'));

		$kpis = $this->model_extension_module_egeser_visitor_report->getKpis($date_from, $date_to);
		$il_counts = $this->model_extension_module_egeser_visitor_report->getVisitorsByIl($date_from, $date_to);
		$il_ranking = array_slice($this->model_extension_module_egeser_visitor_report->getIlRanking($date_from, $date_to), 0, 8);

		$data['heading_title'] = $this->language->get('heading_title');
		$data['visitor_count'] = (int)$kpis['visitors'];
		$data['il_svg_raw'] = file_get_contents(DIR_IMAGE . 'egeser/turkey-map.svg');
		$data['il_style_css'] = EgeserGeoIl::buildStyleCss($il_counts);
		$data['il_counts_json'] = EgeserGeoIl::buildCountsJson($il_counts);
		$data['il_ranking'] = $il_ranking;
		$data['map_counts_id'] = 'egeser-map-counts-dashboard';
		$data['report_url'] = $this->url->link('extension/module/egeser_visitor_report/dashboard', 'token=' . $this->session->data['token'], true);

		return $this->load->view('extension/dashboard/egeser_map_info', $data);
	}

	public function install() {
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', $this->permission_key);
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', $this->permission_key);

		require_once(DIR_SYSTEM . 'library/egeser_visitor_schema.php');
		EgeserVisitorSchema::install($this->db);
	}

	public function uninstall() {
		// Ziyaretci verisi kasitli olarak silinmez; sadece widget kaydi kalkar.
	}
}
