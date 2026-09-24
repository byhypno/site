<?php echo $header; ?>
<?php
$ega_css_path = DIR_TEMPLATE . 'egeser/stylesheet/egeser-hakkimizda-v2-1.css';
$ega_css_version = is_file($ega_css_path) ? filemtime($ega_css_path) : time();
?>
<link rel="stylesheet" href="catalog/view/theme/egeser/stylesheet/egeser-hakkimizda-v2-1.css?v=<?php echo $ega_css_version; ?>">

<div class="ega">
  <div class="container">
    <ul class="breadcrumb ega-breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
      <?php } ?>
    </ul>

    <section class="ega-hero">
      <div class="ega-hero__content">
        <h1>Kurumsal Prefabrik<br><span>Yapı Çözümleri</span></h1>
        <p class="ega-lead">
          İşletmeler, yatırımcılar ve şantiye organizasyonları için kullanım amacına göre
          planlanan prefabrik yapı çözümleri geliştiriyoruz; projelendirme, üretim, sevkiyat
          ve montaj süreçlerini tek merkezden yönetiyoruz.
        </p>
        <div class="ega-pills">
          <span>Ofis &amp; Yönetim</span><span>Yatakhane &amp; Yemekhane</span><span>Şantiye &amp; Sosyal Tesis</span>
        </div>
        <div class="ega-actions">
          <a href="/iletisim" class="ega-btn ega-btn--dark">Kurumsal Teklif Al</a>
          <a href="#kurumsal-yapi-turleri" class="ega-btn-link">Yapı türlerini inceleyin →</a>
        </div>
        <div class="ega-feats">
          <div><h4>Kapasite Odaklı Planlama</h4><p>Kullanıcı sayısı ve fonksiyon ihtiyacına göre yerleşim planı.</p></div>
          <div><h4>Saha Koşullarına Uyum</h4><p>Ulaşım, zemin ve lojistik koşullarına göre sevkiyat planı.</p></div>
          <div><h4>Tek Merkezden Süreç</h4><p>Teklif, üretim ve montajın uyumlu şekilde yönetimi.</p></div>
        </div>
      </div>

      <?php
      $eku_hero_tesis = defined('DIR_IMAGE') && is_file(DIR_IMAGE . 'catalog/egeser/hakkimizda/tesis.jpg');
      $eku_hero_showroom = defined('DIR_IMAGE') && is_file(DIR_IMAGE . 'catalog/egeser/hakkimizda/showroom.jpg');
      $eku_hero_satisofisi = defined('DIR_IMAGE') && is_file(DIR_IMAGE . 'catalog/egeser/hakkimizda/satis-ofisi.jpg');
      ?>
      <?php if ($eku_hero_tesis) { ?>
      <div class="ega-hero__media">
        <figure class="ega-hero__media-main">
          <img src="image/catalog/egeser/hakkimizda/tesis.jpg" alt="Egeser Prefabrik Üretim Tesisi" loading="lazy">
          <figcaption>
            <span>ÜRETİM TESİSİMİZ</span>
            <p>Kurumsal projeler de aynı kontrollü üretim hattında planlanır ve imal edilir.</p>
          </figcaption>
        </figure>
        <?php if ($eku_hero_showroom || $eku_hero_satisofisi) { ?>
        <div class="ega-hero__media-row">
          <?php if ($eku_hero_showroom) { ?>
          <figure>
            <img src="image/catalog/egeser/hakkimizda/showroom.jpg" alt="Egeser Prefabrik Showroom" loading="lazy">
            <figcaption>SHOWROOM ALANI</figcaption>
          </figure>
          <?php } ?>
          <?php if ($eku_hero_satisofisi) { ?>
          <figure>
            <img src="image/catalog/egeser/hakkimizda/satis-ofisi.jpg" alt="Egeser Prefabrik Satış Ofisi" loading="lazy">
            <figcaption>PROJE OFİSİMİZ</figcaption>
          </figure>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
      <?php } ?>
    </section>

    <section class="ega-section" id="kurumsal-yaklasim">
      <div class="ega-section__head">
        <div>
          <div class="ega-kicker">KURUMSAL YAKLAŞIM</div>
          <h2>Kurumsal Prefabrik Yapı Çözümleri</h2>
        </div>
        <p>Yalnızca yapı büyüklüğünü değil; kullanım amacını ve saha koşullarını birlikte değerlendiriyoruz.</p>
      </div>

      <div class="ega-about__text ega-about__text--wide">
        <p>
          Egeser Prefabrik; işletmeler, yatırımcılar, şantiye organizasyonları ve farklı ölçeklerdeki
          kurumlar için kullanım amacına göre planlanan prefabrik yapı çözümleri geliştirir.
          Projelendirme, üretim, sevkiyat ve montaj süreçlerini tek bir bütün olarak ele alır.
        </p>
        <p>
          Kurumsal projelerde yalnızca yapı büyüklüğünü değil; kullanıcı kapasitesi, saha koşulları,
          fonksiyon ihtiyacı, uygulama programı, teknik beklentiler ve gelecekteki kullanım
          senaryolarını da birlikte değerlendiriyoruz.
        </p>
      </div>
    </section>

    <section class="ega-section" id="kurumsal-yapi-turleri">
      <div class="ega-section__head">
        <div>
          <div class="ega-kicker">ÇÖZÜM ALANLARI</div>
          <h2>Kurumsal yapı türleri.</h2>
        </div>
        <p>İhtiyaca uygun kurumsal yapı türünü inceleyin, proje detaylarınızı bizimle paylaşın.</p>
      </div>

      <div class="ega-grid ega-grid--3">
        <a class="ega-region" href="/prefabrik-ofis-ve-yonetim-binalari"><small>İDARİ &amp; TİCARİ</small><h3>Prefabrik Ofis ve Yönetim Binaları</h3><p>İdari birimler, saha yönetimi, proje ofisleri ve operasyon merkezleri için planlanan çözümler.</p></a>
        <a class="ega-region" href="/prefabrik-yatakhane-binalari"><small>KONAKLAMA</small><h3>Prefabrik Yatakhane Binaları</h3><p>Personel konaklama ihtiyacına yönelik oda dağılımı, kapasite ve ortak kullanım alanları.</p></a>
        <a class="ega-region" href="/prefabrik-yemekhane-binalari"><small>TOPLU KULLANIM</small><h3>Prefabrik Yemekhane Binaları</h3><p>Oturma kapasitesi, servis alanları ve dolaşım düzeni dikkate alınarak geliştirilen çözümler.</p></a>
        <a class="ega-region ega-region--dark" href="/prefabrik-santiye-yapilari"><small>SAHA</small><h3>Prefabrik Şantiye Yapıları</h3><p>Şantiye ofisleri, personel alanları, yönetim birimleri ve destek yapılarından oluşan proje bazlı çözümler.</p></a>
        <a class="ega-region" href="/prefabrik-sosyal-tesis-yapilari"><small>ÇOK AMAÇLI</small><h3>Prefabrik Sosyal Tesis Yapıları</h3><p>Ortak kullanım alanları, dinlenme bölümleri, eğitim veya sosyal amaçlı kullanım senaryoları.</p></a>
        <a class="ega-region" href="/ozel-proje-prefabrik-yapilar"><small>ÖZEL PROJE</small><h3>Özel Proje Prefabrik Yapılar</h3><p>Standart çözümlerin dışında kalan; kapasite, planlama, cephe ve saha ihtiyacına göre projelendirilen yapılar.</p></a>
      </div>
    </section>

    <section class="ega-section">
      <div class="ega-section__head">
        <div>
          <div class="ega-kicker">PROJE YAKLAŞIMIMIZ</div>
          <h2>Kurumsal projelerde önceliklerimiz.</h2>
        </div>
        <p>Doğru çözüm; yapı tipinin yanında doğru planlama ve uygulama disiplininden oluşur.</p>
      </div>

      <div class="ega-grid ega-grid--4">
        <div class="ega-card"><span>01</span><h3>Fonksiyon Odaklı Planlama</h3><p>Her alanın kullanım amacına göre planlanması ve gereksiz alan kaybının azaltılması.</p></div>
        <div class="ega-card"><span>02</span><h3>Saha Koşullarına Uyum</h3><p>Ulaşım, montaj alanı, zemin hazırlığı ve lojistik koşullar proje başında değerlendirilir.</p></div>
        <div class="ega-card"><span>03</span><h3>Teknik Netlik</h3><p>Proje kapsamının ve uygulama detaylarının mümkün olduğunca açık tanımlanması.</p></div>
        <div class="ega-card"><span>04</span><h3>Ölçeklenebilir Çözümler</h3><p>Proje büyüklüğü ve kullanım senaryosuna göre farklı yapı tipi ve yerleşim alternatifleri.</p></div>
      </div>
    </section>

    <section class="ega-section">
      <div class="ega-section__head">
        <div>
          <div class="ega-kicker">ÇALIŞMA SÜRECİMİZ</div>
          <h2>İhtiyaçtan teslimata 6 net adım.</h2>
        </div>
        <p>Kurumsal projelerde çok paydaşlı süreci tek merkezden koordine ediyoruz.</p>
      </div>

      <div class="ega-steps">
        <div><span>01</span><b>İhtiyaç &amp; Kapasite Analizi</b><small>Kullanım amacı, yaklaşık m² ve kullanıcı sayısı</small></div>
        <div><span>02</span><b>Yerleşim &amp; Fonksiyon Planı</b><small>Oda, bölüm, dolaşım ve ortak alan ihtiyacı</small></div>
        <div><span>03</span><b>Teknik Kapsam</b><small>Yapı sistemi, yalıtım, doğrama ve tesisat</small></div>
        <div><span>04</span><b>Üretim Planı</b><small>Onaylanan kapsama göre üretim programı</small></div>
        <div><span>05</span><b>Sevkiyat &amp; Saha Uygulaması</b><small>Lojistik ve saha koşullarına göre montaj</small></div>
        <div><span>06</span><b>Teslim &amp; İletişim</b><small>Teslim sonrası satış sonrası iletişim</small></div>
      </div>
    </section>

    <section class="ega-section">
      <div class="ega-section__head">
        <div>
          <div class="ega-kicker">TEKLİF İÇİN</div>
          <h2>Hangi bilgiler gerekir?</h2>
        </div>
        <p>Doğru ön değerlendirme için aşağıdaki bilgileri bizimle paylaşabilirsiniz.</p>
      </div>

      <div class="ega-about__text ega-about__text--wide">
        <p>
          Daha doğru bir ön değerlendirme için firma unvanı, yetkili kişi, proje lokasyonu,
          yapı türü, yaklaşık m², kullanıcı kapasitesi ve varsa plan veya teknik şartname
          bilgilerini paylaşabilirsiniz.
        </p>
        <p>
          Kurumsal projelerde e-posta iletişimi özellikle teknik doküman, teklif ve proje
          bilgilerinin düzenli paylaşılması açısından önemlidir.
        </p>

        <h3>Sık Sorulan Sorular</h3>
        <p><strong>Kurumsal prefabrik yapılar hangi amaçlarla kullanılabilir?</strong><br>
        Ofis ve yönetim binası, yatakhane, yemekhane, şantiye yerleşkesi, sosyal tesis ve proje
        bazlı farklı kullanım ihtiyaçları için planlanabilir.</p>
        <p><strong>Kurumsal projelerde standart model mi uygulanır?</strong><br>
        Projenin ihtiyacına göre standart çözümler değerlendirilebilir; ancak kapasite, yerleşim
        ve teknik beklentilere göre özel planlama da yapılabilir.</p>
        <p><strong>Proje öncesinde hangi bilgiler paylaşılmalıdır?</strong><br>
        Lokasyon, yaklaşık m², kullanım amacı, kullanıcı kapasitesi, ihtiyaç duyulan bölümler ve
        varsa teknik şartname veya plan bilgileri ön değerlendirmeyi hızlandırır.</p>
        <p><strong>Sevkiyat ve montaj süreci proje kapsamına dahil edilebilir mi?</strong><br>
        Sevkiyat ve montaj kapsamı proje detaylarına, lokasyona ve sözleşme şartlarına göre netleştirilir.</p>
      </div>
    </section>

    <section class="ega-cta" id="teklif">
      <div>
        <div class="ega-kicker">PROJE DESTEĞİ</div>
        <h2>Kurumsal projenizi birlikte değerlendirelim.</h2>
        <p>
          Ofis, yatakhane, yemekhane, şantiye yapısı, sosyal tesis veya özel proje ihtiyacınız
          için proje detaylarınızı bizimle paylaşın; kullanım amacı ve teknik beklentilerinize
          göre uygun çözüm alternatiflerini değerlendirelim.
        </p>
      </div>
      <a href="/iletisim" class="ega-btn ega-btn--primary">Teklif / Bilgi Al</a>
    </section>
  </div>
</div>

<?php
$eku_faq_items = array(
  array('Kurumsal prefabrik yapılar hangi amaçlarla kullanılabilir?', 'Ofis ve yönetim binası, yatakhane, yemekhane, şantiye yerleşkesi, sosyal tesis ve proje bazlı farklı kullanım ihtiyaçları için planlanabilir.'),
  array('Kurumsal projelerde standart model mi uygulanır?', 'Projenin ihtiyacına göre standart çözümler değerlendirilebilir; ancak kapasite, yerleşim ve teknik beklentilere göre özel planlama da yapılabilir.'),
  array('Proje öncesinde hangi bilgiler paylaşılmalıdır?', 'Lokasyon, yaklaşık m², kullanım amacı, kullanıcı kapasitesi, ihtiyaç duyulan bölümler ve varsa teknik şartname veya plan bilgileri ön değerlendirmeyi hızlandırır.'),
  array('Sevkiyat ve montaj süreci proje kapsamına dahil edilebilir mi?', 'Sevkiyat ve montaj kapsamı proje detaylarına, lokasyona ve sözleşme şartlarına göre netleştirilir.')
);
$eku_faq_schema = array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array());
foreach ($eku_faq_items as $eku_faq_item) {
  $eku_faq_schema['mainEntity'][] = array(
    '@type' => 'Question',
    'name' => $eku_faq_item[0],
    'acceptedAnswer' => array('@type' => 'Answer', 'text' => $eku_faq_item[1])
  );
}
?>
<script type="application/ld+json"><?php echo json_encode($eku_faq_schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?></script>

<?php echo $footer; ?>
