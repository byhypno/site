<?php echo $header; ?>
<?php
$projects = (isset($egeser_projects) && is_array($egeser_projects)) ? $egeser_projects : array();
$project_types = (isset($egeser_project_types) && is_array($egeser_project_types)) ? $egeser_project_types : array();
$project_locations = (isset($egeser_project_locations) && is_array($egeser_project_locations)) ? $egeser_project_locations : array();
?>
<main id="content" class="eg-page eg-projects-page">
  <div class="eg-container">
    <nav class="eg-breadcrumb" aria-label="Breadcrumb"><ol><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ol></nav>

    <header class="eg-projects-hero">
      <span class="eg-section__eyebrow">TAMAMLANAN PROJELER</span>
      <h1><?php echo $heading_title; ?></h1>
      <p>Gerçek uygulamalarımızı yapı türü ve lokasyona göre inceleyin. Bireysel konutlardan kurumsal prefabrik yapılara kadar tamamladığımız projeleri teknik kapsamlarıyla sunuyoruz.</p>
      <div class="eg-projects-hero__actions"><a class="eg-btn eg-btn--primary" href="#eg-projects-grid">Projeleri İncele</a><a class="eg-btn eg-btn--outline" href="#eg-project-offer">Proje Teklifi Al</a></div>
    </header>

    <section class="eg-project-filters" aria-label="Proje filtreleri">
      <div class="eg-project-filter-group"><strong>Yapı Türü</strong><button type="button" class="is-active" data-project-filter="type" data-value="all">Tümü</button><?php foreach ($project_types as $type) { ?><button type="button" data-project-filter="type" data-value="<?php echo htmlspecialchars($type['slug'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo $type['name']; ?></button><?php } ?></div>
      <div class="eg-project-filter-group"><strong>Lokasyon</strong><button type="button" class="is-active" data-project-filter="location" data-value="all">Tümü</button><?php foreach ($project_locations as $location) { ?><button type="button" data-project-filter="location" data-value="<?php echo htmlspecialchars($location['slug'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo $location['name']; ?></button><?php } ?></div>
    </section>

    <section id="eg-projects-grid" class="eg-projects-archive" aria-labelledby="eg-projects-title">
      <div class="eg-section__heading"><div><span class="eg-section__eyebrow">REFERANSLAR</span><h2 id="eg-projects-title">Uygulamalarımız</h2><p>Proje kartından detay sayfasına geçerek yapı özelliklerini ve uygulama görsellerini inceleyebilirsiniz.</p></div></div>
      <?php if ($projects) { ?>
      <div class="eg-projects-grid">
        <?php foreach ($projects as $project) { ?>
        <article class="eg-project-archive-card" data-project-type="<?php echo isset($project['type_slug']) ? htmlspecialchars($project['type_slug'], ENT_QUOTES, 'UTF-8') : ''; ?>" data-project-location="<?php echo isset($project['location_slug']) ? htmlspecialchars($project['location_slug'], ENT_QUOTES, 'UTF-8') : ''; ?>">
          <a class="eg-project-archive-card__media" href="<?php echo $project['href']; ?>"><?php if (!empty($project['thumb'])) { ?><img src="<?php echo $project['thumb']; ?>" alt="<?php echo htmlspecialchars(strip_tags($project['name']), ENT_QUOTES, 'UTF-8'); ?>" width="720" height="480" loading="lazy" decoding="async"><?php } ?></a>
          <div class="eg-project-archive-card__body">
            <div class="eg-project-archive-card__meta"><?php if (!empty($project['type'])) { ?><span><?php echo $project['type']; ?></span><?php } ?><?php if (!empty($project['location'])) { ?><span><?php echo $project['location']; ?></span><?php } ?></div>
            <h3><a href="<?php echo $project['href']; ?>"><?php echo $project['name']; ?></a></h3>
            <?php if (!empty($project['summary'])) { ?><p><?php echo $project['summary']; ?></p><?php } ?>
            <div class="eg-project-specs"><?php if (!empty($project['area'])) { ?><span><?php echo $project['area']; ?></span><?php } ?><?php if (!empty($project['plan'])) { ?><span><?php echo $project['plan']; ?></span><?php } ?><?php if (!empty($project['year'])) { ?><span><?php echo $project['year']; ?></span><?php } ?></div>
            <a class="eg-section__link" href="<?php echo $project['href']; ?>">Projeyi İncele →</a>
          </div>
        </article>
        <?php } ?>
      </div>
      <p class="eg-project-filter-empty" hidden>Bu filtrelerle eşleşen proje bulunamadı.</p>
      <?php } else { ?><div class="eg-empty"><p>Projeler dinamik entegrasyon tamamlandığında burada listelenecek.</p></div><?php } ?>
    </section>

    <section class="eg-project-local-seo"><span class="eg-section__eyebrow">PROJE DENEYİMİ</span><h2>İzmir ve Ege Bölgesi'nde Prefabrik Yapı Uygulamaları</h2><div class="eg-richtext"><p>Egeser Prefabrik; bireysel prefabrik evlerden ofis, yönetim binası, yatakhane, yemekhane ve proje bazlı kurumsal yapılara kadar farklı kullanım amaçlarında üretim ve montaj hizmetleri sunar. Bu alan yalnızca gerçek, doğrulanmış proje lokasyonları ve uygulama detaylarıyla zenginleştirilmelidir.</p></div></section>

    <section id="eg-project-offer" class="eg-category-offer"><div class="eg-offer"><div class="eg-offer__content"><span class="eg-section__eyebrow">YENİ PROJE</span><h2>Benzer Bir Proje İçin Teklif Alın</h2><p>Yapı türünü, yaklaşık m² bilgisini ve proje lokasyonunu iletin; bireysel veya kurumsal ihtiyacınıza göre satış ekibimiz sizinle iletişime geçsin.</p></div><?php if (isset($egeser_lead_form_html)) { echo $egeser_lead_form_html; } else { ?><a class="eg-btn eg-btn--primary" href="index.php?route=information/contact">Teklif Talebi Oluştur</a><?php } ?></div></section>
  </div>
</main>
<script type="application/ld+json"><?php $items=array();$pos=1;foreach($breadcrumbs as $b){$items[]=array('@type'=>'ListItem','position'=>$pos++,'name'=>strip_tags($b['text']),'item'=>$b['href']);}echo json_encode(array('@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>$items),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?></script>
<?php if ($projects) { ?><script type="application/ld+json"><?php $list=array();$p=1;foreach($projects as $project){$list[]=array('@type'=>'ListItem','position'=>$p++,'url'=>$project['href'],'name'=>strip_tags($project['name']));}echo json_encode(array('@context'=>'https://schema.org','@type'=>'ItemList','name'=>strip_tags($heading_title),'itemListElement'=>$list),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?></script><?php } ?>
<?php echo $footer; ?>
