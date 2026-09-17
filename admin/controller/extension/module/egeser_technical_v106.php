<?php
class ControllerExtensionModuleEgeserTechnicalV106 extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_technical_v106');
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
            'href' => $this->url->link('extension/module/egeser_technical_v106', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/module/egeser_technical_v106', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

        $data['success'] = '';
        $data['error_warning'] = '';

        $language_id = (int)$this->config->get('config_language_id');
        $q = $this->db->query("SELECT information_id, title, meta_title, meta_description, description FROM " . DB_PREFIX . "information_description WHERE language_id = '" . $language_id . "' AND title = 'Teknik Bilgiler' ORDER BY information_id ASC LIMIT 1");

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
            if (!$this->user->hasPermission('modify', 'extension/module/egeser_technical_v106')) {
                $data['error_warning'] = $this->language->get('error_permission');
            } elseif (empty($this->request->post['confirm'])) {
                $data['error_warning'] = $this->language->get('error_confirm');
            } elseif (!$data['found']) {
                $data['error_warning'] = $this->language->get('error_not_found');
            } else {
                $description = '<section class="eg-tech-intro">
  <h2>Prefabrik Yapı Teknik Bilgileri</h2>
  <p>Prefabrik yapıların performansı yalnızca dış görünüşe değil; taşıyıcı sistem, duvar ve çatı yapısı, yalıtım, doğrama, elektrik ve sıhhi tesisat çözümleri ile saha uygulamasının birbiriyle uyumlu olmasına bağlıdır.</p>
  <p>Bu sayfa; prefabrik ev ve kurumsal prefabrik yapı projelerinde teknik kapsamı değerlendirirken dikkat edilmesi gereken temel başlıkları anlaşılır biçimde açıklamak amacıyla hazırlanmıştır. Nihai teknik özellikler proje, kullanım amacı, lokasyon ve sözleşme kapsamına göre netleştirilir.</p>
</section>

<nav class="eg-tech-jump" aria-label="Teknik bilgi bölümleri">
  <a href="#yapi-sistemi">Yapı Sistemi</a>
  <a href="#duvar-sistemi">Duvar Sistemi</a>
  <a href="#cati-sistemi">Çatı Sistemi</a>
  <a href="#yalitim">Isı ve Ses Yalıtımı</a>
  <a href="#dograma">PVC Doğrama</a>
  <a href="#elektrik">Elektrik</a>
  <a href="#tesisat">Sıhhi Tesisat</a>
  <a href="#zemin">Temel / Zemin</a>
  <a href="#montaj">Montaj</a>
  <a href="#bakim">Bakım</a>
</nav>

<section id="yapi-sistemi" class="eg-tech-section">
  <h2>Prefabrik Yapı Sistemi Nasıl Çalışır?</h2>
  <p>Prefabrik yapılarda ana prensip, yapı elemanlarının kontrollü üretim sürecinde hazırlanması ve proje sahasında planlı biçimde bir araya getirilmesidir. Taşıyıcı sistem, duvar panelleri, çatı, doğrama ve tesisat bileşenleri proje kapsamında birbirini tamamlayan bir sistem olarak ele alınmalıdır.</p>
  <div class="eg-tech-grid">
    <article><h3>Taşıyıcı Sistem</h3><p>Yapının yüklerini güvenli biçimde aktaran ana sistemdir. Kesitler ve bağlantı detayları proje boyutu ve kullanım amacına göre belirlenir.</p></article>
    <article><h3>Modüler Üretim Mantığı</h3><p>Üretim aşamalarının kontrollü ortamda yürütülmesi, saha montajında hız ve tekrar edilebilir kalite açısından avantaj sağlar.</p></article>
    <article><h3>Proje Uyumu</h3><p>Mimari plan, teknik sistemler ve saha koşulları birbirinden bağımsız değerlendirilmemelidir.</p></article>
  </div>
</section>

<section id="duvar-sistemi" class="eg-tech-section">
  <h2>Prefabrik Duvar Sistemi</h2>
  <p>Duvar sistemi; dış ortam koşullarına karşı koruma, iç mekân konforu, ısı ve ses performansı ile yüzey dayanımı açısından önemli bir bileşendir. Duvar kalınlığı, panel yapısı, iç ve dış yüzey malzemeleri projenin ihtiyacına göre belirlenmelidir.</p>
  <p>Teknik değerlendirmede yalnızca duvar kalınlığına değil; katman yapısına, birleşim detaylarına, yalıtım malzemesine ve uygulama kalitesine birlikte bakılmalıdır.</p>
</section>

<section id="cati-sistemi" class="eg-tech-section">
  <h2>Prefabrik Çatı Sistemi</h2>
  <p>Çatı sistemi yapıyı yağış, rüzgâr, güneş ve sıcaklık değişimlerine karşı koruyan temel yapı bileşenlerinden biridir. Çatı formu, eğim, su tahliyesi, kaplama sistemi ve birleşim detayları proje bölgesinin iklim koşulları dikkate alınarak planlanmalıdır.</p>
  <div class="eg-tech-note"><strong>Önemli:</strong> Çatı performansında yalnızca kaplama malzemesi değil, doğru eğim, birleşim detayları ve su tahliye çözümü de belirleyicidir.</div>
</section>

<section id="yalitim" class="eg-tech-section">
  <h2>Isı ve Ses Yalıtımı</h2>
  <p>Prefabrik yapılarda konfor seviyesini belirleyen başlıca unsurlardan biri yalıtımdır. Duvar, çatı, doğrama ve birleşim noktalarının birlikte değerlendirilmesi gerekir. Tek bir bileşenin iyi olması, tüm yapı için yeterli performans anlamına gelmez.</p>
  <p>Yalıtım seviyesi; yapının kullanılacağı bölge, sürekli veya dönemsel kullanım, iç mekân beklentileri ve enerji tüketimi hedeflerine göre belirlenmelidir.</p>
</section>

<section id="dograma" class="eg-tech-section">
  <h2>PVC Doğrama ve Cam Sistemleri</h2>
  <p>Pencere ve kapı doğramaları; ısı kaybı, hava sızdırmazlığı, gün ışığı, kullanım konforu ve cephe görünümü üzerinde doğrudan etkilidir. Profil yapısı, cam kombinasyonu, açılım tipi ve montaj detayları proje ihtiyaçlarına göre seçilmelidir.</p>
  <p>Doğrama ölçülerinin yalnızca estetik açıdan değil, oda kullanım düzeni, havalandırma ve cephe oranları açısından da değerlendirilmesi önemlidir.</p>
</section>

<section id="elektrik" class="eg-tech-section">
  <h2>Elektrik Tesisatı</h2>
  <p>Elektrik altyapısı; aydınlatma, priz grupları, sigorta panosu, kablo güzergâhları ve varsa özel cihaz ihtiyaçları dikkate alınarak projelendirilmelidir. Kurumsal yapılarda kullanıcı sayısı ve ekipman yükleri daha ayrıntılı değerlendirme gerektirebilir.</p>
  <p>Son bağlantılar ve saha tarafındaki enerji beslemesi, proje ve uygulama kapsamına göre ilgili teknik gerekliliklere uygun biçimde tamamlanmalıdır.</p>
</section>

<section id="tesisat" class="eg-tech-section">
  <h2>Sıhhi Tesisat</h2>
  <p>Temiz su ve atık su tesisatı; mutfak, banyo, WC, duş ve diğer ıslak hacimlerin konumuna göre planlanır. Boru güzergâhları, bağlantı noktaları, erişilebilirlik ve saha altyapısı uygulama öncesinde netleştirilmelidir.</p>
  <p>Kurumsal yapılarda kullanıcı kapasitesi arttıkça tesisat planlaması da buna göre ölçeklendirilmelidir.</p>
</section>

<section id="zemin" class="eg-tech-section">
  <h2>Temel ve Zemin Hazırlığı</h2>
  <p>Prefabrik yapının sahadaki performansı için zemin hazırlığı kritik bir adımdır. Yapı oturum alanı, kot, drenaj, taşıma kapasitesi, saha erişimi ve temel çözümü uygulama öncesinde değerlendirilmelidir.</p>
  <p>Uygun olmayan veya hazırlığı tamamlanmamış zemin; montaj, kapı-pencere ayarları, su tahliyesi ve uzun dönem kullanım üzerinde olumsuz etki oluşturabilir.</p>
</section>

<section id="montaj" class="eg-tech-section">
  <h2>Prefabrik Yapı Montaj Süreci</h2>
  <ol class="eg-tech-steps">
    <li><strong>Saha kontrolü:</strong> Ulaşım, montaj alanı ve zemin hazırlığı değerlendirilir.</li>
    <li><strong>Sevkiyat:</strong> Üretimi tamamlanan yapı elemanları planlanan programa göre sahaya ulaştırılır.</li>
    <li><strong>Taşıyıcı ve yapı elemanlarının montajı:</strong> Proje sırasına göre ana yapı sistemi oluşturulur.</li>
    <li><strong>Çatı, doğrama ve tamamlayıcı işler:</strong> Yapının dış kabuğu ve kullanım bileşenleri tamamlanır.</li>
    <li><strong>Tesisat ve kontroller:</strong> Proje kapsamındaki elektrik ve sıhhi tesisat işleri kontrol edilir.</li>
    <li><strong>Teslim öncesi kontrol:</strong> Tamamlanan yapı genel uygulama açısından gözden geçirilir.</li>
  </ol>
</section>

<section id="bakim" class="eg-tech-section">
  <h2>Bakım ve Kullanım</h2>
  <p>Prefabrik yapılarda düzenli kontrol ve bakım, yapı elemanlarının kullanım ömrü ve performansı açısından önemlidir. Çatı ve yağmur suyu tahliye noktaları, dış yüzeyler, doğramalar, silikon ve birleşim detayları belirli aralıklarla gözden geçirilmelidir.</p>
  <p>Yapıda sonradan yapılacak delme, kesme, ağır ekipman sabitleme veya tesisat değişiklikleri taşıyıcı sistem ve yalıtım detayları dikkate alınmadan uygulanmamalıdır.</p>
</section>

<section class="eg-tech-faq">
  <h2>Prefabrik Yapılar Hakkında Teknik Sık Sorulan Sorular</h2>

  <details>
    <summary>Prefabrik yapıların duvar kalınlığı her projede aynı mıdır?</summary>
    <p>Hayır. Duvar sistemi; proje tipi, kullanım amacı, iklim koşulları ve teknik şartlara göre farklılaşabilir. Yalnızca kalınlık değil, katman yapısı ve uygulama detayları da değerlendirilmelidir.</p>
  </details>

  <details>
    <summary>Prefabrik yapıda yalıtım yeterli olur mu?</summary>
    <p>Doğru duvar ve çatı sistemi, uygun doğrama, doğru birleşim detayları ve kaliteli uygulama birlikte ele alındığında iyi bir ısı ve ses performansı hedeflenebilir. Gerekli seviye proje bölgesine ve kullanım amacına göre belirlenmelidir.</p>
  </details>

  <details>
    <summary>Prefabrik yapı için beton zemin gerekli midir?</summary>
    <p>Temel ve zemin çözümü proje koşullarına göre belirlenir. Yapının oturacağı alanın düzgün, uygun kotta ve taşıma açısından yeterli olması önemlidir. Nihai temel çözümü proje ve saha değerlendirmesiyle netleştirilmelidir.</p>
  </details>

  <details>
    <summary>Elektrik ve su tesisatı yapı içinde hazırlanabilir mi?</summary>
    <p>Proje kapsamına göre elektrik ve sıhhi tesisat altyapısı yapı içinde planlanabilir. Saha tarafındaki ana enerji, temiz su ve atık su bağlantıları ayrıca değerlendirilmelidir.</p>
  </details>

  <details>
    <summary>Montaj süresi ne kadar sürer?</summary>
    <p>Süre; yapı büyüklüğü, proje tipi, saha erişimi, hava koşulları ve teknik kapsam gibi değişkenlere bağlıdır. Bu nedenle kesin süre proje planı üzerinden belirlenmelidir.</p>
  </details>
</section>

<section class="eg-tech-final-cta">
  <h2>Projenizin Teknik Kapsamını Birlikte Değerlendirelim</h2>
  <p>Prefabrik ev veya kurumsal prefabrik yapı projeniz için kullanım amacı, yaklaşık m², uygulama lokasyonu ve teknik beklentilerinizi paylaşabilirsiniz. Uygun yapı sistemi ve teknik kapsam proje özelinde değerlendirilir.</p>
</section>';
                $meta_title = 'Prefabrik Yapı Teknik Bilgileri | Egeser Prefabrik';
                $meta_description = 'Prefabrik yapı sistemi, duvar, çatı, yalıtım, PVC doğrama, elektrik, sıhhi tesisat, temel, montaj ve bakım hakkında teknik bilgiler.';
                $meta_keyword = 'prefabrik yapı teknik bilgiler, prefabrik duvar sistemi, prefabrik çatı sistemi, prefabrik yalıtım, prefabrik montaj, prefabrik temel';

                $this->db->query("UPDATE " . DB_PREFIX . "information_description SET
                    description = '" . $this->db->escape($description) . "',
                    meta_title = '" . $this->db->escape($meta_title) . "',
                    meta_description = '" . $this->db->escape($meta_description) . "',
                    meta_keyword = '" . $this->db->escape($meta_keyword) . "'
                    WHERE information_id = '" . (int)$data['information_id'] . "'
                    AND language_id = '" . $language_id . "'");

                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link('extension/module/egeser_technical_v106', 'token=' . $this->session->data['token'], true));
            }
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/egeser_technical_v106', $data));
    }

    public function install() {
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/egeser_technical_v106');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/egeser_technical_v106');
    }

    public function uninstall() {
        // İçerik bilinçli olarak silinmez.
    }
}
