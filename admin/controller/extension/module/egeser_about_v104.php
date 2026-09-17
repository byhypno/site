<?php
class ControllerExtensionModuleEgeserAboutV104 extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_about_v104');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_intro'] = $this->language->get('text_intro');
        $data['text_status'] = $this->language->get('text_status');
        $data['button_apply'] = $this->language->get('button_apply');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['entry_confirm'] = $this->language->get('entry_confirm');

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/egeser_about_v104', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/module/egeser_about_v104', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

        $data['success'] = '';
        $data['error_warning'] = '';

        $language_id = (int)$this->config->get('config_language_id');
        $q = $this->db->query("SELECT information_id, title, meta_title, meta_description, description FROM " . DB_PREFIX . "information_description WHERE language_id = '" . $language_id . "' AND title = 'Hakkımızda' ORDER BY information_id ASC LIMIT 1");

        if ($q->num_rows) {
            $data['found'] = true;
            $data['information_id'] = (int)$q->row['information_id'];
            $data['current_title'] = $q->row['title'];
            $data['current_meta_title'] = $q->row['meta_title'];
            $data['current_meta_description'] = $q->row['meta_description'];
            $data['has_description'] = trim(strip_tags(html_entity_decode($q->row['description'], ENT_QUOTES, 'UTF-8'))) !== '';
        } else {
            $data['found'] = false;
            $data['information_id'] = 0;
            $data['current_title'] = '';
            $data['current_meta_title'] = '';
            $data['current_meta_description'] = '';
            $data['has_description'] = false;
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            if (!$this->user->hasPermission('modify', 'extension/module/egeser_about_v104')) {
                $data['error_warning'] = $this->language->get('error_permission');
            } elseif (empty($this->request->post['confirm'])) {
                $data['error_warning'] = $this->language->get('error_confirm');
            } elseif (!$data['found']) {
                $data['error_warning'] = $this->language->get('error_not_found');
            } else {
                $description = '<section class="eg-about-intro">
  <h2>Egeser Prefabrik Hakkında</h2>
  <p>Egeser Prefabrik; bireysel yaşam alanlarından kurumsal prefabrik yapılara kadar farklı ihtiyaçlara yönelik projelendirme, üretim, sevkiyat ve montaj süreçlerini tek çatı altında ele alan bir yapı çözüm markasıdır.</p>
  <p>İzmir / Kemalpaşa merkezli çalışma yapımızla, müşterilerimizin kullanım amacı, arazi koşulları, ihtiyaç duyduğu alan ve proje beklentilerini birlikte değerlendirerek uygun prefabrik yapı çözümünü oluşturmayı hedefliyoruz.</p>
</section>

<section>
  <h2>Bireysel ve Kurumsal Prefabrik Yapı Çözümleri</h2>
  <p>Prefabrik yapıyı yalnızca bir ürün olarak değil, ihtiyaca göre planlanması gereken bütüncül bir proje olarak ele alıyoruz. Bireysel kullanıcılar için prefabrik ev çözümleri geliştirirken; işletmeler ve kurumlar için ofis, yönetim binası, yatakhane, yemekhane, şantiye yapıları, sosyal tesisler ve özel proje yapıları üzerinde çalışıyoruz.</p>
  <div class="eg-about-grid">
    <div>
      <h3>Bireysel Yapılar</h3>
      <p>Tek katlı ve çift katlı prefabrik evlerde kullanım alışkanlıkları, oda planı, yapı büyüklüğü ve uygulama bölgesi birlikte değerlendirilir.</p>
    </div>
    <div>
      <h3>Kurumsal Yapılar</h3>
      <p>Kurumsal projelerde kapasite, fonksiyon, saha koşulları, uygulama programı ve teknik ihtiyaçlara göre proje yaklaşımı oluşturulur.</p>
    </div>
  </div>
</section>

<section>
  <h2>Çalışma Yaklaşımımız</h2>
  <p>Her projenin aynı olmadığını biliyoruz. Bu nedenle ilk görüşmeden montaj sonrasına kadar mümkün olduğunca açık, ölçülebilir ve takip edilebilir bir süreç yürütmeye önem veriyoruz.</p>
  <ul>
    <li><strong>İhtiyaç analizi:</strong> Kullanım amacı, m² beklentisi, oda veya bölüm ihtiyacı ve uygulama yeri belirlenir.</li>
    <li><strong>Projelendirme:</strong> Yapının planı ve teknik kapsamı ihtiyaca göre netleştirilir.</li>
    <li><strong>Üretim:</strong> Belirlenen proje ve teknik şartlara göre üretim süreci yürütülür.</li>
    <li><strong>Sevkiyat ve montaj:</strong> Saha koşulları ve proje planına uygun şekilde uygulama gerçekleştirilir.</li>
    <li><strong>Satış sonrası iletişim:</strong> Teslim sonrasında ihtiyaç duyulan konularda iletişim sürdürülür.</li>
  </ul>
</section>

<section>
  <h2>Neden Egeser Prefabrik?</h2>
  <div class="eg-about-grid">
    <div><h3>Tek Noktadan Süreç Yönetimi</h3><p>Projelendirme, üretim, sevkiyat ve montaj adımlarının birbiriyle uyumlu ilerlemesini hedefliyoruz.</p></div>
    <div><h3>İhtiyaca Göre Çözüm</h3><p>Hazır bir modeli herkese uygulamak yerine kullanım amacı ve proje beklentisine göre seçenekleri değerlendiriyoruz.</p></div>
    <div><h3>Bireysel + Kurumsal Deneyim</h3><p>Prefabrik evlerden kurumsal yapılara kadar farklı kullanım senaryolarını aynı yapı yaklaşımı içinde ele alıyoruz.</p></div>
    <div><h3>Şeffaf İletişim</h3><p>Teknik kapsam, süreç ve uygulama detaylarının proje başlangıcında mümkün olduğunca netleştirilmesine önem veriyoruz.</p></div>
  </div>
</section>

<section>
  <h2>Projenizi Birlikte Değerlendirelim</h2>
  <p>Prefabrik ev veya kurumsal prefabrik yapı projeniz için kullanım amacınızı, yaklaşık m² ihtiyacınızı ve uygulama bölgesini bizimle paylaşabilirsiniz. Ekibimiz ihtiyaçlarınızı değerlendirerek uygun çözüm için sizinle iletişime geçer.</p>
</section>';
                $meta_title = 'Hakkımızda | Egeser Prefabrik';
                $meta_description = 'Egeser Prefabrik hakkında bilgi alın. Bireysel prefabrik evlerden kurumsal yapılara kadar projelendirme, üretim, sevkiyat ve montaj çözümleri.';
                $meta_keyword = 'Egeser Prefabrik, hakkımızda, prefabrik yapı, prefabrik ev, kurumsal prefabrik yapı';

                $this->db->query("UPDATE " . DB_PREFIX . "information_description SET
                    description = '" . $this->db->escape($description) . "',
                    meta_title = '" . $this->db->escape($meta_title) . "',
                    meta_description = '" . $this->db->escape($meta_description) . "',
                    meta_keyword = '" . $this->db->escape($meta_keyword) . "'
                    WHERE information_id = '" . (int)$data['information_id'] . "'
                    AND language_id = '" . $language_id . "'");

                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link('extension/module/egeser_about_v104', 'token=' . $this->session->data['token'], true));
            }
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/egeser_about_v104', $data));
    }

    public function install() {
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/egeser_about_v104');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/egeser_about_v104');
    }

    public function uninstall() {
        // İçeriği bilinçli olarak silmez.
    }
}
