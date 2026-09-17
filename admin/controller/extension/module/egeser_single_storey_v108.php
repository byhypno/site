<?php
class ControllerExtensionModuleEgeserSingleStoreyV108 extends Controller {
    public function index() {
        $this->load->language('extension/module/egeser_single_storey_v108');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_intro'] = $this->language->get('text_intro');
        $data['button_apply'] = $this->language->get('button_apply');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['entry_confirm'] = $this->language->get('entry_confirm');

        $data['breadcrumbs'] = array(
            array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)),
            array('text' => $this->language->get('text_extension'), 'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)),
            array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/module/egeser_single_storey_v108', 'token=' . $this->session->data['token'], true))
        );

        $data['action'] = $this->url->link('extension/module/egeser_single_storey_v108', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
        $data['success'] = '';
        $data['error_warning'] = '';

        $language_id = (int)$this->config->get('config_language_id');

        $q = $this->db->query("SELECT category_id, name, meta_title, meta_description, description
            FROM " . DB_PREFIX . "category_description
            WHERE language_id = '" . $language_id . "'
              AND name = 'Tek Katlı Prefabrik Evler'
            ORDER BY category_id ASC LIMIT 1");

        $data['found'] = $q->num_rows ? true : false;
        $data['category_id'] = $q->num_rows ? (int)$q->row['category_id'] : 0;
        $data['current_name'] = $q->num_rows ? $q->row['name'] : '';
        $data['current_meta_title'] = $q->num_rows ? $q->row['meta_title'] : '';
        $data['has_description'] = $q->num_rows && trim(strip_tags(html_entity_decode($q->row['description'], ENT_QUOTES, 'UTF-8'))) !== '';

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (!$this->user->hasPermission('modify', 'extension/module/egeser_single_storey_v108')) {
                $data['error_warning'] = $this->language->get('error_permission');
            } elseif (empty($this->request->post['confirm'])) {
                $data['error_warning'] = $this->language->get('error_confirm');
            } elseif (!$data['found']) {
                $data['error_warning'] = $this->language->get('error_not_found');
            } else {
                $description = '<section class="eg-cat-seo-block">
  <h2>Tek Katlı Prefabrik Ev Modelleri</h2>
  <p>Tek katlı prefabrik evler; yaşam alanlarının tek seviyede çözümlenmesi, erişimin kolay olması ve planın ihtiyaca göre şekillendirilebilmesi nedeniyle bireysel konut projelerinde sık değerlendirilen yapı seçeneklerinden biridir. Oda sayısı, toplam kullanım alanı, salon-mutfak ilişkisi, ıslak hacimlerin yerleşimi ve dış mekân bağlantısı proje ihtiyacına göre planlanabilir.</p>
  <p>Egeser Prefabrik\'te tek katlı prefabrik ev projesi yalnızca metrekare üzerinden ele alınmaz. Arazinin bulunduğu bölge, kullanım amacı, yıl boyu veya dönemsel kullanım, aile büyüklüğü, plan beklentisi, saha erişimi ve teknik kapsam birlikte değerlendirilir.</p>
</section>

<section class="eg-cat-seo-block">
  <h2>Tek Katlı Prefabrik Ev Planlamasında Nelere Dikkat Edilir?</h2>
  <p>Tek katlı bir prefabrik evin kullanışlı olması yalnızca toplam m² değerine bağlı değildir. Oda ve koridor oranları, gün ışığı, kapı-pencere konumları, mutfak kullanım senaryosu, banyo yerleşimi ve mobilya dolaşım alanları planın günlük yaşam performansını doğrudan etkiler.</p>
  <div class="eg-cat-seo-cards">
    <div><h3>Kullanım Amacı</h3><p>Sürekli yaşam, yazlık, bağ evi veya dönemsel kullanım gibi ihtiyaçlar plan kararlarını etkiler.</p></div>
    <div><h3>Oda Sayısı ve m²</h3><p>1+1, 2+1, 3+1 veya farklı plan ihtiyaçları toplam alan ve oda dağılımıyla birlikte değerlendirilir.</p></div>
    <div><h3>Arazi ve Uygulama Bölgesi</h3><p>Saha erişimi, zemin hazırlığı, sevkiyat ve montaj koşulları proje başlangıcında ele alınır.</p></div>
    <div><h3>Teknik Kapsam</h3><p>Duvar, çatı, doğrama, yalıtım, elektrik ve sıhhi tesisat kapsamı proje özelliklerine göre netleştirilir.</p></div>
  </div>
</section>

<section class="eg-cat-seo-block">
  <h2>Tek Katlı Prefabrik Evlerin Avantajları</h2>
  <p>Tek seviyeli planlama, merdiven ihtiyacını ortadan kaldırdığı için günlük kullanımda kolaylık sağlar. Yaşam, yatak odaları, mutfak ve ıslak hacimlerin aynı katta bulunması; çocuklu aileler, ileri yaş kullanıcılar ve erişilebilirlik beklentisi olan projeler için önemli bir avantaj olabilir.</p>
  <p>Prefabrik yapı sisteminin kontrollü üretim yaklaşımı sayesinde proje, üretim ve saha uygulaması birbiriyle koordineli yürütülebilir. Bununla birlikte her projenin üretim süresi, teknik kapsamı ve montaj programı aynı değildir; yapı büyüklüğü, saha koşulları ve seçilen özelliklere göre planlama yapılmalıdır.</p>
</section>

<section class="eg-cat-seo-block">
  <h2>Tek Katlı Prefabrik Ev Fiyatını Neler Belirler?</h2>
  <p>Tek katlı prefabrik ev fiyatları yalnızca metrekareye bakılarak sağlıklı biçimde karşılaştırılamaz. Plan yapısı, toplam alan, oda sayısı, duvar ve çatı sistemi, doğrama özellikleri, iç mekân kapsamı, elektrik ve sıhhi tesisat, sevkiyat mesafesi, montaj koşulları ve projeye özel talepler toplam maliyeti etkileyebilir.</p>
  <p>Bu nedenle doğru teklif için uygulama lokasyonu, yaklaşık m², istenen oda sayısı ve varsa özel taleplerin paylaşılması önerilir.</p>
</section>

<section class="eg-cat-seo-block">
  <h2>İzmir ve Manisa İçin Tek Katlı Prefabrik Ev Çözümleri</h2>
  <p>İzmir merkezli çalışma yapımızla özellikle İzmir, Manisa ve çevre bölgelerde tek katlı prefabrik ev taleplerini proje ve saha koşullarıyla birlikte değerlendiriyoruz. Lokasyon; sevkiyat, zemin hazırlığı, montaj alanı ve uygulama programı açısından önem taşır.</p>
  <p>Her arazinin erişim ve zemin koşulu farklı olabileceğinden, yalnızca model seçimi değil uygulama bölgesinin teknik değerlendirmesi de proje sürecinin bir parçasıdır.</p>
</section>

<section class="eg-cat-seo-block">
  <h2>Tek Katlı Prefabrik Ev Projesi Nasıl İlerler?</h2>
  <ol class="eg-cat-seo-steps">
    <li><strong>İhtiyaç belirleme:</strong> Yaklaşık m², oda sayısı, kullanım amacı ve uygulama bölgesi paylaşılır.</li>
    <li><strong>Plan değerlendirmesi:</strong> Mevcut modeller veya ihtiyaca uygun plan alternatifleri değerlendirilir.</li>
    <li><strong>Teknik kapsam:</strong> Yapı sistemi, yalıtım, doğrama, tesisat ve diğer proje detayları netleştirilir.</li>
    <li><strong>Teklif ve onay:</strong> Proje kapsamına göre teklif hazırlanır ve onaylanan çalışma üretim planına alınır.</li>
    <li><strong>Üretim ve sevkiyat:</strong> Yapı elemanları üretim programına göre hazırlanır ve sahaya sevk edilir.</li>
    <li><strong>Montaj ve teslim:</strong> Saha koşulları uygun olduğunda montaj tamamlanır ve teslim öncesi kontroller yapılır.</li>
  </ol>
</section>

<section class="eg-cat-seo-block eg-cat-seo-links">
  <h2>Prefabrik Ev Seçiminizi Detaylandırın</h2>
  <p>Tek katlı modellerin yanında farklı ihtiyaçlar için <a href="cift-katli-prefabrik-evler">çift katlı prefabrik evleri</a> inceleyebilir, yapı sistemi hakkında daha ayrıntılı bilgi için <a href="teknik-bilgiler">prefabrik yapı teknik bilgileri</a> sayfasına göz atabilir ve tamamlanan uygulamalar için <a href="referanslar">projelerimiz ve referanslarımızı</a> inceleyebilirsiniz.</p>
</section>';
                $meta_title = 'Tek Katlı Prefabrik Ev Modelleri | Egeser Prefabrik';
                $meta_description = 'Tek katlı prefabrik ev modelleri, plan seçenekleri, proje süreci ve fiyatı etkileyen kriterler. İzmir ve Manisa için prefabrik ev çözümleri.';
                $meta_keyword = 'tek katlı prefabrik ev, tek katlı prefabrik ev modelleri, prefabrik ev modelleri, izmir prefabrik ev, manisa prefabrik ev';

                $this->db->query("UPDATE " . DB_PREFIX . "category_description SET
                    description = '" . $this->db->escape($description) . "',
                    meta_title = '" . $this->db->escape($meta_title) . "',
                    meta_description = '" . $this->db->escape($meta_description) . "',
                    meta_keyword = '" . $this->db->escape($meta_keyword) . "'
                    WHERE category_id = '" . (int)$data['category_id'] . "'
                    AND language_id = '" . $language_id . "'");

                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link('extension/module/egeser_single_storey_v108', 'token=' . $this->session->data['token'], true));
            }
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/egeser_single_storey_v108', $data));
    }

    public function install() {
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/egeser_single_storey_v108');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/egeser_single_storey_v108');
    }

    public function uninstall() {}
}
