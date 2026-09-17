<?php echo $header; ?>
<link rel="stylesheet" href="catalog/view/theme/egeser/stylesheet/egeser-about-v104.css">
<link rel="stylesheet" href="catalog/view/theme/egeser/stylesheet/egeser-corporate-v105.css">
<link rel="stylesheet" href="catalog/view/theme/egeser/stylesheet/egeser-technical-v106.css">
<link rel="stylesheet" href="catalog/view/theme/egeser/stylesheet/egeser-projects-v107.css">
<main id="content" class="eg-page eg-article-page">
  <div class="eg-container">
    <nav class="eg-breadcrumb" aria-label="Breadcrumb"><ol><?php foreach($breadcrumbs as $breadcrumb){ ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ol></nav>
    <?php echo $content_top; ?>
    <article class="eg-content-page eg-authority-article">
      <header class="eg-authority-article__header">
        <?php if ($heading_title === 'Hakkımızda') { ?>
          <span class="eg-section__eyebrow">EGESER PREFABRİK</span>
          <h1><?php echo $heading_title; ?></h1>
          <p class="eg-authority-article__intro">Bireysel yaşam alanlarından kurumsal prefabrik yapılara kadar projelendirme, üretim, sevkiyat ve montaj süreçlerine bütüncül yaklaşım.</p>
        <?php } elseif ($heading_title === 'Kurumsal') { ?>
          <span class="eg-section__eyebrow">KURUMSAL PREFABRİK ÇÖZÜMLER</span>
          <h1>Kurumsal Prefabrik Yapılar</h1>
          <p class="eg-authority-article__intro">Ofis, yatakhane, yemekhane, şantiye, sosyal tesis ve özel proje ihtiyaçlarına yönelik planlı prefabrik yapı çözümleri.</p>
        <?php } elseif ($heading_title === 'Teknik Bilgiler') { ?>
          <span class="eg-section__eyebrow">PREFABRİK YAPI BİLGİ MERKEZİ</span>
          <h1>Prefabrik Yapı Teknik Bilgileri</h1>
          <p class="eg-authority-article__intro">Duvar ve çatı sisteminden yalıtım, tesisat, temel hazırlığı, montaj ve bakıma kadar prefabrik yapılarda teknik olarak bilinmesi gereken temel konular.</p>
        <?php } elseif ($heading_title === 'Projelerimiz ve Referanslar' || $heading_title === 'Projelerimiz') { ?>
          <span class="eg-section__eyebrow">TAMAMLANAN UYGULAMALAR</span>
          <h1>Prefabrik Yapı Projeleri ve Referanslar</h1>
          <p class="eg-authority-article__intro">Bireysel prefabrik evlerden kurumsal yapılara kadar farklı kullanım senaryolarına göre tamamlanan seçili proje örnekleri.</p>
        <?php } else { ?>
          <span class="eg-section__eyebrow">EGESER PREFABRİK BİLGİ MERKEZİ</span>
          <h1><?php echo $heading_title; ?></h1>
          <p class="eg-authority-article__intro">Prefabrik yapı seçimi, teknik sistemler, uygulama ve kullanım süreçleri hakkında açıklayıcı bilgiler.</p>
        <?php } ?>
      </header>
      <div class="eg-authority-article__layout">
        <div class="eg-richtext eg-authority-article__content"><?php echo $description; ?></div>
        <aside class="eg-authority-article__aside" aria-label="İletişim">
          <strong>Projeniz için teknik destek alın</strong>
          <p>Bireysel veya kurumsal yapınız için ihtiyaçlarınızı ekibimizle paylaşın.</p>
          <a class="eg-btn eg-btn--primary" href="/iletisim">Teklif / Bilgi Al</a>
          <?php if (!empty($telephone)) { ?><a class="eg-authority-article__phone" href="tel:<?php echo preg_replace('/[^0-9+]/', '', $telephone); ?>">☎ <?php echo $telephone; ?></a><?php } ?>
        </aside>
      </div>
    </article>
    <?php echo $content_bottom; ?>
  </div>
</main>


<?php if (!empty($egeser_schema_url)) { ?>
<script type="application/ld+json"><?php
$eg_page_schema = array(
  '@context' => 'https://schema.org',
  '@type' => !empty($egeser_schema_type) ? $egeser_schema_type : 'WebPage',
  'name' => !empty($egeser_schema_name) ? strip_tags($egeser_schema_name) : strip_tags($heading_title),
  'url' => $egeser_schema_url
);
if (!empty($egeser_schema_description)) { $eg_page_schema['description'] = $egeser_schema_description; }
echo json_encode($eg_page_schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?></script>
<script type="application/ld+json"><?php
$eg_bc_items = array(); $eg_bc_pos = 1;
foreach ($breadcrumbs as $eg_bc) {
  $eg_bc_items[] = array('@type'=>'ListItem','position'=>$eg_bc_pos++,'name'=>trim(strip_tags($eg_bc['text'])),'item'=>html_entity_decode($eg_bc['href'], ENT_QUOTES, 'UTF-8'));
}
echo json_encode(array('@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>$eg_bc_items), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?></script>
<?php } ?>

<?php echo $footer; ?>


<?php if ($heading_title === 'Teknik Bilgiler') { ?>
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"FAQPage",
  "mainEntity":[
    {
      "@type":"Question",
      "name":"Prefabrik yapıların duvar kalınlığı her projede aynı mıdır?",
      "acceptedAnswer":{"@type":"Answer","text":"Hayır. Duvar sistemi; proje tipi, kullanım amacı, iklim koşulları ve teknik şartlara göre farklılaşabilir. Yalnızca kalınlık değil, katman yapısı ve uygulama detayları da değerlendirilmelidir."}
    },
    {
      "@type":"Question",
      "name":"Prefabrik yapıda yalıtım yeterli olur mu?",
      "acceptedAnswer":{"@type":"Answer","text":"Doğru duvar ve çatı sistemi, uygun doğrama, doğru birleşim detayları ve kaliteli uygulama birlikte ele alındığında iyi bir ısı ve ses performansı hedeflenebilir. Gerekli seviye proje bölgesine ve kullanım amacına göre belirlenmelidir."}
    },
    {
      "@type":"Question",
      "name":"Prefabrik yapı için beton zemin gerekli midir?",
      "acceptedAnswer":{"@type":"Answer","text":"Temel ve zemin çözümü proje koşullarına göre belirlenir. Yapının oturacağı alanın düzgün, uygun kotta ve taşıma açısından yeterli olması önemlidir. Nihai temel çözümü proje ve saha değerlendirmesiyle netleştirilmelidir."}
    },
    {
      "@type":"Question",
      "name":"Elektrik ve su tesisatı yapı içinde hazırlanabilir mi?",
      "acceptedAnswer":{"@type":"Answer","text":"Proje kapsamına göre elektrik ve sıhhi tesisat altyapısı yapı içinde planlanabilir. Saha tarafındaki ana enerji, temiz su ve atık su bağlantıları ayrıca değerlendirilmelidir."}
    },
    {
      "@type":"Question",
      "name":"Montaj süresi ne kadar sürer?",
      "acceptedAnswer":{"@type":"Answer","text":"Süre; yapı büyüklüğü, proje tipi, saha erişimi, hava koşulları ve teknik kapsam gibi değişkenlere bağlıdır. Bu nedenle kesin süre proje planı üzerinden belirlenmelidir."}
    }
  ]
}
</script>
<?php } ?>

<?php if ($heading_title === 'Teknik Bilgiler') { ?>
<script src="catalog/view/theme/egeser/javascript/egeser-technical-v1061.js" defer></script>
<?php } ?>


<?php if ($heading_title === 'Projelerimiz ve Referanslar' || $heading_title === 'Projelerimiz') { ?>
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"FAQPage",
  "mainEntity":[
    {
      "@type":"Question",
      "name":"Projeler sadece İzmir ve Manisa'da mı uygulanıyor?",
      "acceptedAnswer":{"@type":"Answer","text":"İzmir ve Manisa ana çalışma bölgelerimiz arasında yer almakla birlikte, proje kapsamı ve saha koşullarına göre farklı bölgeler de değerlendirilebilir."}
    },
    {
      "@type":"Question",
      "name":"Referanslardaki bir proje aynen uygulanabilir mi?",
      "acceptedAnswer":{"@type":"Answer","text":"Mevcut projeler fikir vermek amacıyla incelenebilir; ancak saha, kullanım amacı, plan ihtiyacı ve teknik beklentilere göre projede uyarlama gerekebilir."}
    },
    {
      "@type":"Question",
      "name":"Projeye başlamadan önce hangi bilgiler gerekir?",
      "acceptedAnswer":{"@type":"Answer","text":"Uygulama lokasyonu, yaklaşık m², kullanım amacı, oda veya bölüm ihtiyacı ve varsa özel teknik beklentilerin paylaşılması ön değerlendirmeyi hızlandırır."}
    },
    {
      "@type":"Question",
      "name":"Proje fotoğrafları sonradan eklenebilir mi?",
      "acceptedAnswer":{"@type":"Answer","text":"Evet. Her proje kartına veya ileride oluşturulacak proje detay sayfalarına gerçek uygulama fotoğrafları, planlar ve teknik özetler eklenebilir."}
    }
  ]
}
</script>
<?php } ?>


<?php if ($heading_title === 'Projelerimiz ve Referanslar' || $heading_title === 'Projelerimiz') { ?>
<script src="catalog/view/theme/egeser/javascript/egeser-projects-v107.js" defer></script>
<?php } ?>
