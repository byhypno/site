<?php echo $header; ?>
<style>
.eg-post{padding:30px 0 70px;background:#fff}.eg-post__container{width:min(1050px,calc(100% - 32px));margin:auto}
.eg-post__crumb{font-size:14px;margin-bottom:22px}.eg-post__crumb a{color:#555;text-decoration:none}.eg-post__header{max-width:900px;margin-bottom:26px}
.eg-post__header h1{font-size:clamp(30px,5vw,48px);line-height:1.12;margin:0 0 12px}.eg-post__date{font-size:14px;color:#777}
.eg-post__image{margin:26px 0;border-radius:18px;overflow:hidden;background:#f3f3f3}.eg-post__image img{display:block;width:100%;height:auto}
.eg-post__layout{display:grid;grid-template-columns:minmax(0,1fr) 270px;gap:38px;align-items:start}.eg-post__content{font-size:17px;line-height:1.78;color:#2b2b2b}
.eg-post__content h1{display:none}.eg-post__content h2{font-size:28px;line-height:1.25;margin:36px 0 14px}.eg-post__content h3{font-size:21px;margin:28px 0 10px}
.eg-post__content img{max-width:100%;height:auto}.eg-post__content a{color:#b71c1c}.eg-post__content ul,.eg-post__content ol{padding-left:24px}
.eg-post__aside{position:sticky;top:22px;border:1px solid #e7e7e7;border-radius:16px;padding:20px;background:#fafafa}.eg-post__aside strong{display:block;font-size:18px;margin-bottom:8px}
.eg-post__aside p{font-size:14px;color:#555;line-height:1.55}.eg-post__aside a{display:block;text-align:center;text-decoration:none;border-radius:10px;padding:11px 12px;margin-top:9px;font-weight:700}
.eg-post__aside .primary{background:#c62828;color:#fff}.eg-post__aside .secondary{border:1px solid #d0d0d0;color:#222;background:#fff}
@media(max-width:860px){.eg-post__layout{grid-template-columns:1fr}.eg-post__aside{position:static}}
</style>
<main class="eg-post">
  <div class="eg-post__container">
    <nav class="eg-post__crumb" aria-label="Breadcrumb">
      <?php foreach ($breadcrumbs as $i => $breadcrumb) { ?>
        <?php if ($i) { ?> / <?php } ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
      <?php } ?>
    </nav>

    <?php echo $content_top; ?>

    <article>
      <header class="eg-post__header">
        <h1><?php echo $heading_title; ?></h1>
        <?php if ($date) { ?><div class="eg-post__date">Yayın tarihi: <?php echo $date; ?></div><?php } ?>
      </header>

      <?php if ($image) { ?><div class="eg-post__image"><img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($heading_title, ENT_QUOTES, 'UTF-8'); ?>" width="1200" height="700"></div><?php } ?>

      <?php
      // EGESER - Yazı içeriğinin başındaki tekrar eden <h1> etiketini kaldırır.
      // Sayfa zaten kendi <h1>'ini basıyor; içerikte ikinci bir <h1> olması
      // arama motoru denetim araçlarında "çift H1" hatasına yol açıyordu.
      $eg_post_body = preg_replace('#<h1[^>]*>.*?</h1>#is', '', $description, 1);
      ?>
      <div class="eg-post__layout">
        <div class="eg-post__content"><?php echo $eg_post_body; ?></div>
        <aside class="eg-post__aside">
          <strong>Prefabrik projeniz için bilgi alın</strong>
          <p>İhtiyacınız olan metrekare, plan ve kurulum lokasyonunu paylaşın; uygun çözümü birlikte değerlendirelim.</p>
          <a class="primary" href="/iletisim">Teklif / Bilgi Al</a>
          <a class="secondary" href="/prefabrik-yapilar">Prefabrik Yapıları İncele</a>
        </aside>
      </div>
    </article>

    <?php echo $content_bottom; ?>
  </div>
</main>
<script type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?></script>
<script type="application/ld+json"><?php echo json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?></script>
<?php echo $footer; ?>
