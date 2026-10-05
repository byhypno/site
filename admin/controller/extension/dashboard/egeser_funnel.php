<?php
class ControllerExtensionDashboardEgeserFunnel extends Controller {
	private $error = array();
	private $permission_key = 'extension/dashboard/egeser_funnel';

	public function index() {
		$this->load->language('extension/dashboard/egeser_funnel');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_egeser_funnel', $this->request->post);

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
		$data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/dashboard/egeser_funnel', 'token=' . $this->session->data['token'], true));

		$data['action'] = $this->url->link('extension/dashboard/egeser_funnel', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true);

		$data['dashboard_egeser_funnel_width'] = isset($this->request->post['dashboard_egeser_funnel_width']) ? $this->request->post['dashboard_egeser_funnel_width'] : $this->config->get('dashboard_egeser_funnel_width');
		$data['dashboard_egeser_funnel_status'] = isset($this->request->post['dashboard_egeser_funnel_status']) ? $this->request->post['dashboard_egeser_funnel_status'] : $this->config->get('dashboard_egeser_funnel_status');
		$data['dashboard_egeser_funnel_sort_order'] = isset($this->request->post['dashboard_egeser_funnel_sort_order']) ? $this->request->post['dashboard_egeser_funnel_sort_order'] : $this->config->get('dashboard_egeser_funnel_sort_order');

		$data['columns'] = array();
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard/egeser_funnel_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->permission_key)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function dashboard() {
		$this->load->language('extension/dashboard/egeser_funnel');
		$this->load->model('extension/module/egeser_visitor_report');
		$this->model_extension_module_egeser_visitor_report->install();

		$date_to = date('Y-m-d');
		$date_from = date('Y-m-d', strtotime('-29 days'));

		$stages = $this->model_extension_module_egeser_visitor_report->getFunnel($date_from, $date_to);

		$max = $stages ? max(array_column($stages, 'count')) : 0;
		foreach ($stages as &$stage) {
			$stage['percent'] = $max > 0 ? round(($stage['count'] / $max) * 100) : 0;
		}
		unset($stage);

		$data['heading_title'] = $this->language->get('heading_title');
		$data['stages'] = $stages;
		$data['report_url'] = $this->url->link('extension/module/egeser_visitor_report/dashboard', 'token=' . $this->session->data['token'], true);

		return $this->load->view('extension/dashboard/egeser_funnel_info', $data);
	}

	public function install() {
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', $this->permission_key);
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', $this->permission_key);

		$this->load->model('extension/extension');
		$this->model_extension_extension->install('dashboard', 'egeser_funnel');

		require_once(DIR_SYSTEM . 'library/egeser_visitor_schema.php');
		EgeserVisitorSchema::install($this->db);
	}

	public function uninstall() {
		$this->load->model('extension/extension');
		$this->model_extension_extension->uninstall('dashboard', 'egeser_funnel');
	}
}
