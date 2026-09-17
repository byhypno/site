<?php
class ControllerExtensionModuleEgeserProjectsV107 extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_projects_v107');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_intro'] = $this->language->get('text_intro');
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
            'href' => $this->url->link('extension/module/egeser_projects_v107', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/module/egeser_projects_v107', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
        $data['success'] = '';
        $data['error_warning'] = '';

        $language_id = (int)$this->config->get('config_language_id');
        $q = $this->db->query("SELECT information_id, title, meta_title, meta_description, description
            FROM " . DB_PREFIX . "information_description
            WHERE language_id = '" . $language_id . "'
              AND (title = 'Projelerimiz ve Referanslar' OR title = 'Projelerimiz')
            ORDER BY information_id ASC LIMIT 1");

        if ($q->num_rows) {
            $data['found'] = true;
            $data['information_id'] = (int)$q->row['information_id'];
            $data['current_title'] = $q->row['title'];
            $data['current_meta_title'] = $q->row['meta_title'];
            $data['has_description'] = trim(strip_tags(html_entity_decode($q->row['description'], ENT_QUOTES, 'UTF-8'))) !== '';
        } else {
            $data['found'] = false;
            $data['information_id'] = 0;
            $data['current_title'] = '';
            $data['current_meta_title'] = '';
            $data['has_description'] = false;
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (!$this->user->hasPermission('modify', 'extension/module/egeser_projects_v107')) {
                $data['error_warning'] = $this->language->get('error_permission');
            } elseif (empty($this->request->post['confirm'])) {
                $data['error_warning'] = $this->language->get('error_confirm');
            } elseif (!$data['found']) {
                $data['error_warning'] = $this->language->get('error_not_found');
            } else {
                $description = '<section class="eg-projects-intro">
  <h2>Tamamlanan Prefabrik Yapı Projeleri</h2>
  <p>Egeser Prefabrik olarak bireysel yaşam alanlarından kurumsal yapılara kadar farklı ölçeklerde prefabrik yapı projeleri üzerinde çalışıyoruz. Bu sayfada farklı kullanım amaçlarına, büyüklüklere ve uygulama bölgelerine göre tamamlanan projelerden seçili örnekleri inceleyebilirsiniz.</p>
  <p>Her proje; kullanım amacı, saha koşulları, yapı büyüklüğü, planlama ihtiyaçları ve teknik kapsam doğrultusunda değerlendirilir. Buradaki örnekler, farklı ihtiyaçlara nasıl çözüm geliştirilebildiğini göstermek amacıyla sunulmaktadır.</p>
</section>

<nav class="eg-projects-jump" aria-label="Proje türleri">
  <a href="#bireysel-projeler">Bireysel Projeler</a>
  <a href="#kurumsal-projeler">Kurumsal Projeler</a>
  <a href="#proje-sureci">Proje Süreci</a>
  <a href="#proje-sss">Sık Sorulan Sorular</a>
</nav>

<section id="bireysel-projeler" class="eg-projects-section">
  <span class="eg-section__eyebrow">BİREYSEL REFERANSLAR</span>
  <h2>Prefabrik Ev Projelerimizden Seçmeler</h2>
  <p>Tek katlı ve çift katlı prefabrik ev projelerinde planlama; oda sayısı, yaşam alışkanlıkları, uygulama bölgesi ve saha koşullarına göre şekillenir.</p>

  <div class="eg-projects-grid">
    <article class="eg-project-card">
      <div class="eg-project-card__meta"><span>Manisa / Karaoğlanlı</span><span>85 m²</span></div>
      <h3>85 m² Prefabrik Ev</h3>
      <p>2 oda 1 salon planlamaya sahip bireysel yaşam projesi. Zemin hazırlığı ve saha altyapısı proje kapsamı doğrultusunda değerlendirilmiştir.</p>
      <div class="eg-project-card__tags"><span>2+1</span><span>Prefabrik Ev</span><span>Bireysel</span></div>
    </article>

    <article class="eg-project-card">
      <div class="eg-project-card__meta"><span>Manisa / Gördes</span><span>52 m²</span></div>
      <h3>52 m² Prefabrik Ev</h3>
      <p>Kompakt yaşam ihtiyacına yönelik planlanan, kullanım alanı ve dolaşım verimliliğinin öne çıktığı prefabrik konut uygulaması.</p>
      <div class="eg-project-card__tags"><span>Prefabrik Ev</span><span>Kompakt Plan</span></div>
    </article>

    <article class="eg-project-card">
      <div class="eg-project-card__meta"><span>Manisa / Demirci</span><span>63 m²</span></div>
      <h3>63 m² Prefabrik Ev</h3>
      <p>4 oda ve salon-mutfak kurgusuyla aile kullanımına yönelik planlanan, balkonlu prefabrik yaşam alanı.</p>
      <div class="eg-project-card__tags"><span>4 Oda</span><span>Balkon</span><span>Aile Kullanımı</span></div>
    </article>

    <article class="eg-project-card">
      <div class="eg-project-card__meta"><span>Manisa / Paşaköy</span><span>74 m²</span></div>
      <h3>74 m² Prefabrik Ev</h3>
      <p>Yaşam alanlarının dengeli dağılımına odaklanan orta ölçekli prefabrik ev projesi.</p>
      <div class="eg-project-card__tags"><span>Prefabrik Ev</span><span>Yaşam Alanı</span></div>
    </article>
  </div>
</section>

<section id="kurumsal-projeler" class="eg-projects-section">
  <span class="eg-section__eyebrow">KURUMSAL REFERANSLAR</span>
  <h2>Kurumsal Prefabrik Yapı Uygulamaları</h2>
  <p>Ofis, yönetim binası, yatakhane, yemekhane, şantiye yapıları ve özel proje ihtiyaçlarında kapasite, fonksiyon, saha programı ve teknik beklentiler birlikte ele alınır.</p>

  <div class="eg-projects-grid eg-projects-grid--corporate">
    <article class="eg-project-card">
      <h3>Prefabrik Ofis ve Yönetim Yapıları</h3>
      <p>İdari birimler, saha yönetimi, proje ofisleri ve operasyon merkezleri için kullanım senaryosuna göre planlanan çözümler.</p>
      <div class="eg-project-card__tags"><span>Ofis</span><span>Yönetim</span><span>Kurumsal</span></div>
    </article>

    <article class="eg-project-card">
      <h3>Yatakhane ve Personel Yapıları</h3>
      <p>Personel kapasitesi, oda düzeni, ortak alanlar ve dolaşım ihtiyaçlarına göre ölçeklendirilebilen prefabrik yapılar.</p>
      <div class="eg-project-card__tags"><span>Yatakhane</span><span>Personel</span></div>
    </article>

    <article class="eg-project-card">
      <h3>Yemekhane ve Sosyal Alanlar</h3>
      <p>Toplu kullanım kapasitesi, servis akışı ve ortak kullanım ihtiyaçlarına göre geliştirilen prefabrik tesis çözümleri.</p>
      <div class="eg-project-card__tags"><span>Yemekhane</span><span>Sosyal Tesis</span></div>
    </article>
  </div>
</section>

<section id="proje-sureci" class="eg-projects-section">
  <h2>Bir Prefabrik Yapı Projesi Nasıl İlerliyor?</h2>
  <ol class="eg-projects-steps">
    <li><strong>İhtiyaç analizi:</strong> Kullanım amacı, yaklaşık m², oda/bölüm ihtiyacı ve proje lokasyonu belirlenir.</li>
    <li><strong>Planlama:</strong> Mimari yerleşim ve kullanım senaryosu netleştirilir.</li>
    <li><strong>Teknik kapsam:</strong> Yapı sistemi, çatı, duvar, doğrama ve tesisat ihtiyaçları değerlendirilir.</li>
    <li><strong>Üretim:</strong> Onaylanan proje ve teknik kapsam doğrultusunda üretim süreci yürütülür.</li>
    <li><strong>Sevkiyat ve montaj:</strong> Saha erişimi ve uygulama programına göre montaj planlanır.</li>
    <li><strong>Teslim ve destek:</strong> Tamamlanan yapı teslim öncesi kontrollerden geçirilir ve satış sonrası iletişim sürdürülür.</li>
  </ol>
</section>

<section class="eg-projects-local">
  <h2>İzmir ve Manisa Bölgesinde Prefabrik Yapı Uygulamaları</h2>
  <p>İzmir merkezli çalışma yapımız sayesinde özellikle İzmir, Manisa ve çevre bölgelerde bireysel ve kurumsal prefabrik yapı taleplerini saha koşullarıyla birlikte değerlendirebiliyoruz. Proje lokasyonu; sevkiyat, montaj programı, saha erişimi ve zemin hazırlığı açısından sürecin önemli bir parçasıdır.</p>
  <p>Farklı il ve ilçelerde gerçekleştirilen uygulamalar, aynı yapı tipinin her sahada aynı şekilde ele alınamayacağını gösterir. Bu nedenle lokasyon, proje başlangıcında mutlaka değerlendirilmelidir.</p>
</section>

<section id="proje-sss" class="eg-projects-faq">
  <h2>Prefabrik Yapı Projeleri Hakkında Sık Sorulan Sorular</h2>

  <details>
    <summary>Projeler sadece İzmir ve Manisa\'da mı uygulanıyor?</summary>
    <p>İzmir ve Manisa ana çalışma bölgelerimiz arasında yer almakla birlikte, proje kapsamı ve saha koşullarına göre farklı bölgeler de değerlendirilebilir.</p>
  </details>

  <details>
    <summary>Referanslardaki bir proje aynen uygulanabilir mi?</summary>
    <p>Mevcut projeler fikir vermek amacıyla incelenebilir; ancak saha, kullanım amacı, plan ihtiyacı ve teknik beklentilere göre projede uyarlama gerekebilir.</p>
  </details>

  <details>
    <summary>Projeye başlamadan önce hangi bilgiler gerekir?</summary>
    <p>Uygulama lokasyonu, yaklaşık m², kullanım amacı, oda veya bölüm ihtiyacı ve varsa özel teknik beklentilerin paylaşılması ön değerlendirmeyi hızlandırır.</p>
  </details>

  <details>
    <summary>Proje fotoğrafları sonradan eklenebilir mi?</summary>
    <p>Evet. Her proje kartına veya ileride oluşturulacak proje detay sayfalarına gerçek uygulama fotoğrafları, planlar ve teknik özetler eklenebilir.</p>
  </details>
</section>

<section class="eg-projects-final-cta">
  <h2>Sizin Projenizi de Birlikte Planlayalım</h2>
  <p>Prefabrik ev veya kurumsal prefabrik yapı ihtiyacınız için proje lokasyonunu, yaklaşık m² bilgisini ve kullanım amacını paylaşın. Uygun yapı yaklaşımını birlikte değerlendirelim.</p>
</section>';
                $meta_title = 'Prefabrik Yapı Projeleri ve Referanslar | Egeser Prefabrik';
                $meta_description = 'İzmir ve Manisa başta olmak üzere tamamlanan prefabrik ev ve kurumsal prefabrik yapı projelerinden seçili referansları inceleyin.';
                $meta_keyword = 'prefabrik projeler, prefabrik referanslar, prefabrik ev projeleri, izmir prefabrik proje, manisa prefabrik proje, kurumsal prefabrik';

                $this->db->query("UPDATE " . DB_PREFIX . "information_description SET
                    description = '" . $this->db->escape($description) . "',
                    meta_title = '" . $this->db->escape($meta_title) . "',
                    meta_description = '" . $this->db->escape($meta_description) . "',
                    meta_keyword = '" . $this->db->escape($meta_keyword) . "'
                    WHERE information_id = '" . (int)$data['information_id'] . "'
                    AND language_id = '" . $language_id . "'");

                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link('extension/module/egeser_projects_v107', 'token=' . $this->session->data['token'], true));
            }
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/egeser_projects_v107', $data));
    }

    public function install() {
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/egeser_projects_v107');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/egeser_projects_v107');
    }

    public function uninstall() {
        // İçerik silinmez.
    }
}
