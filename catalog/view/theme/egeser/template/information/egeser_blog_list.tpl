<?php echo $header; ?>
<style>
.eg-blog{padding:34px 0 70px;background:#fff}.eg-blog__container{width:min(1180px,calc(100% - 32px));margin:auto}
.eg-blog__crumb{font-size:14px;margin-bottom:22px}.eg-blog__crumb a{color:#555;text-decoration:none}.eg-blog__hero{padding:32px;border:1px solid #ececec;border-radius:18px;background:#fafafa;margin-bottom:28px}
.eg-blog__hero h1{margin:0 0 12px;font-size:clamp(30px,5vw,48px);line-height:1.08}.eg-blog__hero p{margin:0;max-width:820px;font-size:17px;line-height:1.7;color:#555}
.eg-blog__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}.eg-blog-card{border:1px solid #e9e9e9;border-radius:16px;overflow:hidden;background:#fff;display:flex;flex-direction:column}
.eg-blog-card__image{aspect-ratio:16/9;background:#f3f3f3;overflow:hidden}.eg-blog-card__image img{width:100%;height:100%;object-fit:cover}.eg-blog-card__body{padding:22px;display:flex;flex-direction:column;flex:1}
.eg-blog-card__date{font-size:13px;color:#777;margin-bottom:8px}.eg-blog-card h2{font-size:22px;line-height:1.3;margin:0 0 12px}.eg-blog-card h2 a{color:#161616;text-decoration:none}
.eg-blog-card p{color:#5b5b5b;line-height:1.65;margin:0 0 18px}.eg-blog-card__link{margin-top:auto;font-weight:700;color:#c62828;text-decoration:none}
@media(max-width:760px){.eg-blog__grid{grid-template-columns:1fr}.eg-blog__hero{padding:24px}}
</style>
<main class="eg-blog">
  <div class="eg-blog__container">
    <nav class="eg-blog__crumb" aria-label="Breadcrumb">
      <?php foreach ($breadcrumbs as $i => $breadcrumb) { ?>
        <?php if ($i) { ?> / <?php } ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
      <?php } ?>
    </nav>

    <?php echo $content_top; ?>

    <header class="eg-blog__hero">
      <h1><?php echo $heading_title; ?></h1>
      <p>Prefabrik ev seçimi, malzeme yapısı, kullanım alanları, deprem güvenliği ve farklı yapı sistemleri hakkında eski siteden korunan rehber içerikleri.</p>
    </header>

    <section class="eg-blog__grid" aria-label="Blog yazıları">
      <?php foreach ($posts as $post) { ?>
      <article class="eg-blog-card">
        <?php if (!empty($post['image'])) { ?><a class="eg-blog-card__image" href="<?php echo $post['href']; ?>"><img src="<?php echo $post['image']; ?>" alt="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" width="640" height="420"></a><?php } ?>
        <div class="eg-blog-card__body">
          <?php if ($post['date']) { ?><div class="eg-blog-card__date"><?php echo $post['date']; ?></div><?php } ?>
          <h2><a href="<?php echo $post['href']; ?>"><?php echo $post['title']; ?></a></h2>
          <p><?php echo $post['excerpt']; ?></p>
          <a class="eg-blog-card__link" href="<?php echo $post['href']; ?>">Yazıyı Oku →</a>
        </div>
      </article>
      <?php } ?>
    </section>

    <?php echo $content_bottom; ?>
  </div>
</main>
<script type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?></script>
<script type="application/ld+json"><?php echo json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?></script>
<?php echo $footer; ?>
