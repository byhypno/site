<?php
class ControllerInformationInformation extends Controller {
	public function index() {
		$this->load->language('information/information');

		$this->load->model('catalog/information');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => 'Ana Sayfa',
			'href' => rtrim($this->config->get('config_url'), '/') . '/'
		);

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$this->document->setTitle($information_info['meta_title']);
			$this->document->setDescription($information_info['meta_description']);
			$this->document->setKeywords($information_info['meta_keyword']);

			// EGESER - Her bilgi sayfası kendi SEO URL'sini canonical olarak kullansın.
			$this->document->addLink(
				$this->url->link('information/information', 'information_id=' . (int)$information_id),
				'canonical'
			);

			$data['breadcrumbs'][] = array(
				'text' => $information_info['title'],
				'href' => $this->url->link('information/information', 'information_id=' .  $information_id)
			);

			$data['heading_title'] = $information_info['title'];

			// EGESER - Bilgi sayfasi WebPage schema verileri.
			$data['egeser_schema_type'] = 'WebPage';
			if ($information_id === 7) {
				$data['egeser_schema_type'] = 'AboutPage';
			} elseif ($information_id === 9) {
				$data['egeser_schema_type'] = 'CollectionPage';
			}
			$data['egeser_schema_url'] = html_entity_decode($this->url->link('information/information', 'information_id=' . (int)$information_id), ENT_QUOTES, 'UTF-8');
			$data['egeser_schema_name'] = $information_info['title'];
			$data['egeser_schema_description'] = trim(strip_tags(html_entity_decode(!empty($information_info['meta_description']) ? $information_info['meta_description'] : $information_info['description'], ENT_QUOTES, 'UTF-8')));

			$data['button_continue'] = $this->language->get('button_continue');

			$data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

			$data['continue'] = '/';

			if ($information_id === 9) {
				// EGESER - Gercek proje fotograflari ve kurumsal is ortagi logolari
				// admin > Extensions > Modules > "Egeser Referanslari" uzerinden eklendiginde
				// bu blok otomatik gorunur; hicbir sey eklenmemisse bos doner.
				// NOT: bu cagri common/header render edilmeden ONCE yapilmali, cunku
				// modul kendi CSS dosyasini $this->document->addStyle() ile ekliyor ve
				// header, o ana kadar eklenmis stilleri <head> icine yazip kapaniyor.
				$data['egeser_references_html'] = '';

				$reference_query = $this->db->query("SELECT `setting` FROM `" . DB_PREFIX . "module` WHERE `code` = 'egeser_references' ORDER BY `module_id` ASC LIMIT 1");

				if ($reference_query->num_rows) {
					$reference_setting = json_decode($reference_query->row['setting'], true);

					if (is_array($reference_setting)) {
						$data['egeser_references_html'] = $this->load->controller('extension/module/egeser_references', $reference_setting);
					}
				}
			}

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			if ($information_id === 9) {
				$this->response->setOutput($this->load->view('information/egeser_projelerimiz_v1_1', $data));
			} elseif ($information_id === 10) {
				$this->response->setOutput($this->load->view('information/egeser_teknik_bilgiler_v1', $data));
			} elseif ($information_id === 7) {
				$this->response->setOutput($this->load->view('information/egeser_hakkimizda_v2', $data));
			} else {
				$this->response->setOutput($this->load->view('information/information', $data));
			}
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('information/information', 'information_id=' . $information_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = '/';

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function agree() {
		$this->load->model('catalog/information');

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$output = '';

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		$this->response->setOutput($output);
	}
}
