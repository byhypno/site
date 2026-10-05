<?php
class ControllerExtensionDashboardEgeserChart extends Controller {
	private $error = array();
	private $permission_key = 'extension/dashboard/egeser_chart';

	public function index() {
		$this->load->language('extension/dashboard/egeser_chart');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_egeser_chart', $this->request->post);

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
		$data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/dashboard/egeser_chart', 'token=' . $this->session->data['token'], true));

		$data['action'] = $this->url->link('extension/dashboard/egeser_chart', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true);

		$data['dashboard_egeser_chart_width'] = isset($this->request->post['dashboard_egeser_chart_width']) ? $this->request->post['dashboard_egeser_chart_width'] : $this->config->get('dashboard_egeser_chart_width');
		$data['dashboard_egeser_chart_status'] = isset($this->request->post['dashboard_egeser_chart_status']) ? $this->request->post['dashboard_egeser_chart_status'] : $this->config->get('dashboard_egeser_chart_status');
		$data['dashboard_egeser_chart_sort_order'] = isset($this->request->post['dashboard_egeser_chart_sort_order']) ? $this->request->post['dashboard_egeser_chart_sort_order'] : $this->config->get('dashboard_egeser_chart_sort_order');

		$data['columns'] = array();
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard/egeser_chart_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->permission_key)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function dashboard() {
		$this->load->language('extension/dashboard/egeser_chart');

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_day'] = $this->language->get('text_day');
		$data['text_week'] = $this->language->get('text_week');
		$data['text_month'] = $this->language->get('text_month');
		$data['text_year'] = $this->language->get('text_year');
		$data['text_visitors'] = $this->language->get('text_visitors');
		$data['text_product_views'] = $this->language->get('text_product_views');
		$data['token'] = $this->session->data['token'];

		return $this->load->view('extension/dashboard/egeser_chart_info', $data);
	}

	public function chart() {
		$this->load->language('extension/dashboard/egeser_chart');
		$this->load->model('extension/module/egeser_visitor_report');
		$this->model_extension_module_egeser_visitor_report->install();

		$range = isset($this->request->get['range']) ? (string)$this->request->get['range'] : 'day';
		if (!in_array($range, array('day', 'week', 'month', 'year'), true)) {
			$range = 'day';
		}

		$visitors = $this->model_extension_module_egeser_visitor_report->getChartSeries('visitors', $range);
		$product_views = $this->model_extension_module_egeser_visitor_report->getChartSeries('product_views', $range);

		$json = array('visitors' => array(), 'product_views' => array(), 'xaxis' => array());
		$json['visitors']['label'] = $this->language->get('text_visitors');
		$json['product_views']['label'] = $this->language->get('text_product_views');
		$json['visitors']['data'] = array();
		$json['product_views']['data'] = array();

		foreach ($visitors as $key => $total) {
			$json['visitors']['data'][] = array($key, $total);
		}
		foreach ($product_views as $key => $total) {
			$json['product_views']['data'][] = array($key, $total);
		}

		if ($range === 'day') {
			for ($i = 0; $i < 24; $i++) $json['xaxis'][] = array($i, $i);
		} elseif ($range === 'week') {
			$date_start = strtotime('-' . date('w') . ' days');
			for ($i = 0; $i < 7; $i++) {
				$date = date('Y-m-d', $date_start + ($i * 86400));
				$json['xaxis'][] = array((int)date('w', strtotime($date)), date('D', strtotime($date)));
			}
		} elseif ($range === 'year') {
			for ($i = 1; $i <= 12; $i++) $json['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i)));
		} else {
			for ($i = 1; $i <= (int)date('t'); $i++) {
				$date = date('Y') . '-' . date('m') . '-' . $i;
				$json['xaxis'][] = array((int)date('j', strtotime($date)), date('d', strtotime($date)));
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
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
		$this->model_extension_extension->install('dashboard', 'egeser_chart');

		require_once(DIR_SYSTEM . 'library/egeser_visitor_schema.php');
		EgeserVisitorSchema::install($this->db);
	}

	public function uninstall() {
		$this->load->model('extension/extension');
		$this->model_extension_extension->uninstall('dashboard', 'egeser_chart');
	}
}
