<?php echo $header; ?>
<link rel="stylesheet" href="catalog/view/theme/egeser/stylesheet/egeser-corporate-v105.css">
<main id="content" class="eg-page eg-article-page">
  <div class="eg-container">
    <nav class="eg-breadcrumb" aria-label="Breadcrumb"><ol><?php foreach($breadcrumbs as $breadcrumb){ ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ol></nav>
    <?php echo $content_top; ?>
    <article class="eg-content-page eg-authority-article">
      <header class="eg-authority-article__header">
        <?php if ($heading_title === 'Kurumsal') { ?>
          <span class="eg-section__eyebrow">KURUMSAL PREFABRİK ÇÖZÜMLER</span>
          <h1>Kurumsal Prefabrik Yapılar</h1>
          <p class="eg-authority-article__intro">Ofis, yatakhane, yemekhane, şantiye, sosyal tesis ve özel proje ihtiyaçlarına yönelik planlı prefabrik yapı çözümleri.</p>
        <?php } elseif ($heading_title === 'KVKK Aydınlatma Metni' || $heading_title === 'Gizlilik Politikası' || $heading_title === 'Çerez Politikası') { ?>
          <span class="eg-section__eyebrow">YASAL BİLGİLENDİRME</span>
          <h1><?php echo $heading_title; ?></h1>
          <p class="eg-authority-article__intro">Kişisel verilerinizin işlenmesi ve web sitemizin kullanımına ilişkin bilgilendirme metni.</p>
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
