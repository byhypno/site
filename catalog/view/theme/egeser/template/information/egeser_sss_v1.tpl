<?php echo $header; ?>
<?php
$esq_css_path = DIR_TEMPLATE . 'egeser/stylesheet/egeser-sss-v1.css';
$esq_css_version = is_file($esq_css_path) ? filemtime($esq_css_path) : time();
?>
<link rel="stylesheet" href="catalog/view/theme/egeser/stylesheet/egeser-sss-v1.css?v=<?php echo $esq_css_version; ?>">

<div class="esq">
  <div class="container">
    <ul class="breadcrumb esq-breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
      <?php } ?>
    </ul>

    <div class="esq-hero">
      <div class="esq-kicker">SIK SORULAN SORULAR</div>
      <h1>Merak ettiklerinize hızlıca ulaşın.</h1>
      <p>
        Fiyatlandırma, teknik detaylar, kurumsal projeler, ödeme seçenekleri ile sevkiyat
        ve montaj süreci hakkında en sık gelen soruları tek sayfada topladık.
      </p>
    </div>

    <nav class="esq-nav" aria-label="SSS kategorileri">
      <a href="/sss#genel">Genel &amp; Fiyatlandırma</a>
      <a href="/sss#kurumsal">Kurumsal Projeler</a>
      <a href="/sss#teknik">Teknik Detaylar</a>
      <a href="/sss#odeme">Ödeme</a>
      <a href="/sss#iletisim">Sevkiyat, Montaj &amp; İletişim</a>
    </nav>

    <?php
    $esq_groups = array(
      array(
        'id' => 'genel',
        'title' => 'Genel & Fiyatlandırma',
        'items' => array(
          array('Prefabrik ev fiyatları nasıl hesaplanır?', 'Fiyat; m², kat sayısı, plan, teknik özellikler, iç donanım, sevkiyat mesafesi, saha koşulları ve montaj kapsamına göre proje bazında belirlenir.'),
          array('Prefabrik ev planında değişiklik yapılabilir mi?', 'Plan değişikliği; taşıyıcı sistem, üretim ve tesisat koşulları birlikte değerlendirilerek proje aşamasında netleştirilebilir.'),
          array('Prefabrik ev için ruhsat gerekir mi?', 'Ruhsat ve imar gereklilikleri arsanın bulunduğu yere ve projenin niteliğine göre değişebilir. Uygulama öncesinde ilgili belediye ve yetkili kurumlarla güncel koşulların doğrulanması gerekir.'),
          array('Prefabrik ev kurulumu ne kadar sürer?', 'Süre; yapının büyüklüğü, proje kapsamı, üretim planı, saha hazırlığı ve montaj koşullarına göre değişir. Net süre teklif ve proje aşamasında belirlenir.'),
          array('Prefabrik evde ısı ve ses yalıtımı nasıl planlanır?', 'Yalıtım performansı; duvar ve çatı sistemi, kullanılan katmanlar, doğrama ve uygulama detaylarının birlikte değerlendirilmesiyle şekillenir.'),
          array('Prefabrik yapı hangi zemine kurulur?', 'Uygun temel ve zemin hazırlığı proje ve saha koşullarına göre belirlenir. Kurulum öncesinde erişim, kot, drenaj ve temel gereksinimleri değerlendirilmelidir.')
        )
      ),
      array(
        'id' => 'kurumsal',
        'title' => 'Kurumsal Projeler',
        'items' => array(
          array('Kurumsal projeler özel ölçüde hazırlanabilir mi?', 'Ofis, yatakhane, yemekhane, şantiye ve sosyal tesis yapılarında kullanım amacı, kapasite ve saha ihtiyaçlarına göre özel proje çalışması yapılabilir.'),
          array('Kurumsal prefabrik yapılar hangi amaçlarla kullanılabilir?', 'Ofis ve yönetim binası, yatakhane, yemekhane, şantiye yerleşkesi, sosyal tesis ve proje bazlı farklı kullanım ihtiyaçları için planlanabilir.'),
          array('Kurumsal projelerde standart model mi uygulanır?', 'Projenin ihtiyacına göre standart çözümler değerlendirilebilir; ancak kapasite, yerleşim ve teknik beklentilere göre özel planlama da yapılabilir.'),
          array('Kurumsal proje öncesinde hangi bilgiler paylaşılmalıdır?', 'Firma unvanı, proje lokasyonu, yapı türü, yaklaşık m², kullanıcı kapasitesi ve varsa plan veya teknik şartname bilgileri ön değerlendirmeyi hızlandırır.'),
          array('Kurumsal projelerde sevkiyat ve montaj süreci kapsama dahil edilebilir mi?', 'Sevkiyat ve montaj kapsamı proje detaylarına, lokasyona ve sözleşme şartlarına göre netleştirilir.')
        )
      ),
      array(
        'id' => 'teknik',
        'title' => 'Teknik Detaylar',
        'items' => array(
          array('Prefabrik yapıların duvar kalınlığı her projede aynı mıdır?', 'Hayır. Duvar sistemi; proje tipi, kullanım amacı, iklim koşulları ve teknik şartlara göre farklılaşabilir. Yalnızca kalınlık değil, katman yapısı ve uygulama detayları da değerlendirilmelidir.'),
          array('Prefabrik yapıda yalıtım yeterli olur mu?', 'Doğru duvar ve çatı sistemi, uygun doğrama, doğru birleşim detayları ve kaliteli uygulama birlikte ele alındığında iyi bir ısı ve ses performansı hedeflenebilir. Gerekli seviye proje bölgesine ve kullanım amacına göre belirlenir.'),
          array('Prefabrik yapı için beton zemin gerekli midir?', 'Temel ve zemin çözümü proje koşullarına göre belirlenir. Yapının oturacağı alanın düzgün, uygun kotta ve taşıma açısından yeterli olması önemlidir. Nihai çözüm saha ve proje değerlendirmesiyle netleştirilmelidir.'),
          array('Elektrik ve su tesisatı yapı içinde hazırlanabilir mi?', 'Proje kapsamına göre elektrik ve sıhhi tesisat altyapısı yapı içinde planlanabilir. Saha tarafındaki ana enerji, temiz su ve atık su bağlantıları ayrıca değerlendirilmelidir.'),
          array('Montaj süresi ne kadar sürer?', 'Süre; yapı büyüklüğü, proje tipi, saha erişimi, hava koşulları ve teknik kapsam gibi değişkenlere bağlıdır. Kesin süre proje planı üzerinden belirlenir.')
        )
      ),
      array(
        'id' => 'odeme',
        'title' => 'Ödeme',
        'items' => array(
          array('Kredi kartına taksitli ödeme yapılabilir mi?', 'Evet. Tüm banka kartlarında geçerli olmak üzere 12 taksite kadar kredi kartına taksitli ödeme seçeneği değerlendirilebilir.'),
          array('Ödeme planı proje teslim takvimiyle nasıl uyumlu hale getirilir?', 'Ödeme planı, projenin üretim ve teslim takvimiyle uyumlu şekilde teklif aşamasında ekibimizle birlikte belirlenir.')
        )
      ),
      array(
        'id' => 'iletisim',
        'title' => 'Sevkiyat, Montaj & İletişim',
        'items' => array(
          array('Teklif almak için hangi bilgileri paylaşmalıyım?', 'Yapı türü, yaklaşık m², uygulama bölgesi ve varsa oda veya kapasite ihtiyacınızı paylaşmanız ilk değerlendirme için yeterlidir.'),
          array('Showroom ziyareti için randevu gerekli mi?', 'Zorunlu olmamakla birlikte doğru ekip arkadaşımızın size zaman ayırabilmesi için ziyaret öncesi telefon veya WhatsApp üzerinden randevu oluşturmanızı öneririz.'),
          array('Kurumsal projeler için teknik doküman gönderebilir miyim?', 'Evet. Proje planı, ihtiyaç listesi ve teknik dokümanlarınızı e-posta veya WhatsApp üzerinden paylaşabilirsiniz.'),
          array('İzmir dışındaki projeler için iletişime geçebilir miyim?', 'Evet. Proje lokasyonu, sevkiyat ve montaj koşullarına göre Ege Bölgesi ve çevre illerde uygulama değerlendirilebilir.'),
          array('Sevkiyat ve montaj hizmeti veriliyor mu?', 'Projenin kapsamı ve kurulum bölgesine göre üretim, sevkiyat ve montaj süreçleri birlikte planlanabilir.')
        )
      )
    );
    ?>

    <?php foreach ($esq_groups as $esq_group) { ?>
    <section class="esq-group" id="<?php echo $esq_group['id']; ?>">
      <h2><?php echo $esq_group['title']; ?></h2>
      <div class="esq-list">
        <?php foreach ($esq_group['items'] as $esq_item) { ?>
        <details class="esq-item">
          <summary><?php echo $esq_item[0]; ?></summary>
          <p><?php echo $esq_item[1]; ?></p>
        </details>
        <?php } ?>
      </div>
    </section>
    <?php } ?>

    <section class="esq-cta">
      <div>
        <h2>Sorunuzu bulamadınız mı?</h2>
        <p>Projenize özel sorularınız için ekibimize doğrudan ulaşabilirsiniz.</p>
      </div>
      <a href="/iletisim" class="esq-btn">Bize Ulaşın</a>
    </section>
  </div>
</div>

<?php
$esq_faq_schema_items = array();
foreach ($esq_groups as $esq_group) {
  foreach ($esq_group['items'] as $esq_item) {
    $esq_faq_schema_items[] = array(
      '@type' => 'Question',
      'name' => $esq_item[0],
      'acceptedAnswer' => array('@type' => 'Answer', 'text' => $esq_item[1])
    );
  }
}
?>
<script type="application/ld+json"><?php
echo json_encode(array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $esq_faq_schema_items), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?></script>
<script type="application/ld+json"><?php
$esq_bc_items = array(); $esq_bc_pos = 1;
foreach ($breadcrumbs as $esq_bc) {
  $esq_bc_items[] = array('@type'=>'ListItem','position'=>$esq_bc_pos++,'name'=>trim(strip_tags($esq_bc['text'])),'item'=>html_entity_decode($esq_bc['href'], ENT_QUOTES, 'UTF-8'));
}
echo json_encode(array('@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>$esq_bc_items), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?></script>

<?php echo $footer; ?>
