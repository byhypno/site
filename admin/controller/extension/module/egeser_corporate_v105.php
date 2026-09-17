<?php
class ControllerExtensionModuleEgeserCorporateV105 extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_corporate_v105');
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
            'href' => $this->url->link('extension/module/egeser_corporate_v105', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/module/egeser_corporate_v105', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

        $data['success'] = '';
        $data['error_warning'] = '';

        $language_id = (int)$this->config->get('config_language_id');
        $q = $this->db->query("SELECT information_id, title, meta_title, meta_description, description FROM " . DB_PREFIX . "information_description WHERE language_id = '" . $language_id . "' AND title = 'Kurumsal' ORDER BY information_id ASC LIMIT 1");

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
            if (!$this->user->hasPermission('modify', 'extension/module/egeser_corporate_v105')) {
                $data['error_warning'] = $this->language->get('error_permission');
            } elseif (empty($this->request->post['confirm'])) {
                $data['error_warning'] = $this->language->get('error_confirm');
            } elseif (!$data['found']) {
                $data['error_warning'] = $this->language->get('error_not_found');
            } else {
                $description = '<section class="eg-corp-intro">
  <h2>Kurumsal Prefabrik Yapı Çözümleri</h2>
  <p>Egeser Prefabrik; işletmeler, yatırımcılar, şantiye organizasyonları ve farklı ölçeklerdeki kurumlar için kullanım amacına göre planlanan prefabrik yapı çözümleri geliştirir. Projelendirme, üretim, sevkiyat ve montaj süreçlerini tek bir bütün olarak ele alır.</p>
  <p>Kurumsal projelerde yalnızca yapı büyüklüğünü değil; kullanıcı kapasitesi, saha koşulları, fonksiyon ihtiyacı, uygulama programı, teknik beklentiler ve gelecekteki kullanım senaryolarını da birlikte değerlendiriyoruz.</p>
</section>

<section>
  <h2>Kurumsal Yapı Türleri</h2>
  <div class="eg-corp-grid">
    <article>
      <h3>Prefabrik Ofis ve Yönetim Binaları</h3>
      <p>İdari birimler, saha yönetimi, proje ofisleri ve operasyon merkezleri için ihtiyaca göre planlanan prefabrik ofis çözümleri.</p>
    </article>
    <article>
      <h3>Prefabrik Yatakhane Binaları</h3>
      <p>Personel konaklama ihtiyacına yönelik oda dağılımı, kapasite ve ortak kullanım alanları dikkate alınarak planlanan yapılar.</p>
    </article>
    <article>
      <h3>Prefabrik Yemekhane Binaları</h3>
      <p>Personel ve toplu kullanım ihtiyacına uygun oturma kapasitesi, servis alanları ve dolaşım düzeni dikkate alınarak geliştirilen çözümler.</p>
    </article>
    <article>
      <h3>Prefabrik Şantiye Yapıları</h3>
      <p>Şantiye ofisleri, personel alanları, yönetim birimleri ve destek yapılarından oluşabilen proje bazlı saha çözümleri.</p>
    </article>
    <article>
      <h3>Prefabrik Sosyal Tesis Yapıları</h3>
      <p>Ortak kullanım alanları, dinlenme bölümleri, eğitim veya sosyal amaçlı kullanım senaryolarına göre geliştirilen prefabrik yapılar.</p>
    </article>
    <article>
      <h3>Özel Proje Prefabrik Yapılar</h3>
      <p>Standart çözümlerin dışında kalan; kapasite, planlama, cephe, fonksiyon ve saha ihtiyacına göre projelendirilen özel yapılar.</p>
    </article>
  </div>
</section>

<section>
  <h2>Kurumsal Projelerde Nasıl Çalışıyoruz?</h2>
  <ol class="eg-corp-steps">
    <li><strong>İhtiyaç ve kapasite analizi:</strong> Yapının kullanım amacı, yaklaşık m² ihtiyacı, kullanıcı sayısı ve proje sahası belirlenir.</li>
    <li><strong>Yerleşim ve fonksiyon planlaması:</strong> Oda, bölüm, dolaşım ve ortak alan ihtiyaçları proje kapsamına göre netleştirilir.</li>
    <li><strong>Teknik kapsam:</strong> Yapı sistemi, yalıtım, doğrama, elektrik, sıhhi tesisat ve proje özelindeki teknik beklentiler değerlendirilir.</li>
    <li><strong>Üretim planı:</strong> Onaylanan proje ve teknik kapsam doğrultusunda üretim süreci planlanır.</li>
    <li><strong>Sevkiyat ve saha uygulaması:</strong> Lojistik ve saha koşulları dikkate alınarak montaj süreci yürütülür.</li>
    <li><strong>Teslim ve satış sonrası iletişim:</strong> Proje tesliminin ardından ihtiyaç duyulan konularda iletişim sürdürülür.</li>
  </ol>
</section>

<section>
  <h2>Kurumsal Prefabrik Yapılarda Proje Yaklaşımımız</h2>
  <div class="eg-corp-grid eg-corp-grid--compact">
    <article><h3>Fonksiyon Odaklı Planlama</h3><p>Her alanın kullanım amacına göre planlanmasını ve gereksiz alan kaybının azaltılmasını hedefliyoruz.</p></article>
    <article><h3>Saha Koşullarına Uyum</h3><p>Ulaşım, montaj alanı, zemin hazırlığı ve lojistik koşullar proje başında değerlendirilir.</p></article>
    <article><h3>Teknik Netlik</h3><p>Proje kapsamının, kullanılacak sistemlerin ve uygulama detaylarının mümkün olduğunca açık tanımlanmasına önem veriyoruz.</p></article>
    <article><h3>Ölçeklenebilir Çözümler</h3><p>Proje büyüklüğü ve kullanım senaryosuna göre farklı yapı tipleri ve yerleşim alternatifleri değerlendirilebilir.</p></article>
  </div>
</section>

<section>
  <h2>Kurumsal Proje Teklifi İçin Hangi Bilgiler Gerekir?</h2>
  <p>Daha doğru bir ön değerlendirme için firma unvanı, yetkili kişi, proje lokasyonu, yapı türü, yaklaşık m², kullanıcı kapasitesi ve varsa plan veya teknik şartname bilgilerini paylaşabilirsiniz.</p>
  <p>Kurumsal projelerde e-posta iletişimi özellikle teknik doküman, teklif ve proje bilgilerinin düzenli paylaşılması açısından önemlidir.</p>
</section>

<section class="eg-corp-faq">
  <h2>Sık Sorulan Sorular</h2>
  <h3>Kurumsal prefabrik yapılar hangi amaçlarla kullanılabilir?</h3>
  <p>Ofis ve yönetim binası, yatakhane, yemekhane, şantiye yerleşkesi, sosyal tesis ve proje bazlı farklı kullanım ihtiyaçları için planlanabilir.</p>

  <h3>Kurumsal projelerde standart model mi uygulanır?</h3>
  <p>Projenin ihtiyacına göre standart çözümler değerlendirilebilir; ancak kapasite, yerleşim ve teknik beklentilere göre özel planlama da yapılabilir.</p>

  <h3>Proje öncesinde hangi bilgiler paylaşılmalıdır?</h3>
  <p>Lokasyon, yaklaşık m², kullanım amacı, kullanıcı kapasitesi, ihtiyaç duyulan bölümler ve varsa teknik şartname veya plan bilgileri ön değerlendirmeyi hızlandırır.</p>

  <h3>Sevkiyat ve montaj süreci proje kapsamına dahil edilebilir mi?</h3>
  <p>Sevkiyat ve montaj kapsamı proje detaylarına, lokasyona ve sözleşme şartlarına göre netleştirilir.</p>
</section>

<section class="eg-corp-final-cta">
  <h2>Kurumsal Projenizi Birlikte Değerlendirelim</h2>
  <p>Ofis, yatakhane, yemekhane, şantiye yapısı, sosyal tesis veya özel proje prefabrik yapı ihtiyacınız için proje detaylarınızı bizimle paylaşabilirsiniz. Ekibimiz kullanım amacı ve teknik beklentilerinize göre uygun çözüm alternatiflerini değerlendirir.</p>
  <p><a class="eg-btn eg-btn--primary" href="#eg-solutions">Kurumsal Proje Teklifi Al</a></p>
</section>';
                $meta_title = 'Kurumsal Prefabrik Yapılar | Egeser Prefabrik';
                $meta_description = 'Ofis, yatakhane, yemekhane, şantiye, sosyal tesis ve özel proje prefabrik yapı çözümleri. Kurumsal projeniz için Egeser Prefabrik ile iletişime geçin.';
                $meta_keyword = 'kurumsal prefabrik yapı, prefabrik ofis, prefabrik yatakhane, prefabrik yemekhane, prefabrik şantiye, prefabrik sosyal tesis';

                $this->db->query("UPDATE " . DB_PREFIX . "information_description SET
                    description = '" . $this->db->escape($description) . "',
                    meta_title = '" . $this->db->escape($meta_title) . "',
                    meta_description = '" . $this->db->escape($meta_description) . "',
                    meta_keyword = '" . $this->db->escape($meta_keyword) . "'
                    WHERE information_id = '" . (int)$data['information_id'] . "'
                    AND language_id = '" . $language_id . "'");

                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link('extension/module/egeser_corporate_v105', 'token=' . $this->session->data['token'], true));
            }
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/egeser_corporate_v105', $data));
    }

    public function install() {
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/egeser_corporate_v105');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/egeser_corporate_v105');
    }

    public function uninstall() {
        // İçerik bilinçli olarak silinmez.
    }
}
