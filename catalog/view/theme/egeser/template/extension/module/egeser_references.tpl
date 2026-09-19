<section class="egref" aria-labelledby="egref-title">
  <div class="egref__container">
    <?php if ($projects) { ?>
    <div class="egref__head">
      <div>
        <span class="egref__eyebrow">PROJELER / REFERANSLAR</span>
        <h2 id="egref-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
      </div>
      <?php if ($subtitle) { ?>
        <p><?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php } ?>
    </div>

    <div class="egref__grid">
      <?php foreach ($projects as $project) { ?>
      <article class="egref__card">
        <?php if ($project['link']) { ?>
          <a class="egref__media" href="<?php echo htmlspecialchars($project['link'], ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php } else { ?>
          <div class="egref__media">
        <?php } ?>

          <?php if ($project['image']) { ?>
            <img src="<?php echo htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8'); ?>"
                 alt="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>"
                 width="760" height="570" loading="lazy" decoding="async">
          <?php } else { ?>
            <div class="egref__placeholder">Proje görseli eklenecek</div>
          <?php } ?>

          <?php if ($project['type']) { ?>
            <span class="egref__badge"><?php echo htmlspecialchars($project['type'], ENT_QUOTES, 'UTF-8'); ?></span>
          <?php } ?>

        <?php if ($project['link']) { ?></a><?php } else { ?></div><?php } ?>

        <div class="egref__body">
          <?php if ($project['eyebrow']) { ?>
            <small><?php echo htmlspecialchars($project['eyebrow'], ENT_QUOTES, 'UTF-8'); ?></small>
          <?php } ?>

          <h3>
            <?php if ($project['link']) { ?><a href="<?php echo htmlspecialchars($project['link'], ENT_QUOTES, 'UTF-8'); ?>"><?php } ?>
            <?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>
            <?php if ($project['link']) { ?></a><?php } ?>
          </h3>

          <?php if ($project['description']) { ?>
            <p><?php echo htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?></p>
          <?php } ?>

          <?php if ($project['location'] || $project['size']) { ?>
          <div class="egref__meta">
            <?php if ($project['location']) { ?><span><?php echo htmlspecialchars($project['location'], ENT_QUOTES, 'UTF-8'); ?></span><?php } ?>
            <?php if ($project['size']) { ?><span><?php echo htmlspecialchars($project['size'], ENT_QUOTES, 'UTF-8'); ?></span><?php } ?>
          </div>
          <?php } ?>

          <?php if ($project['link']) { ?>
            <a class="egref__link" href="<?php echo htmlspecialchars($project['link'], ENT_QUOTES, 'UTF-8'); ?>">Projeyi İncele <span aria-hidden="true">→</span></a>
          <?php } ?>
        </div>
      </article>
      <?php } ?>
    </div>
    <?php } ?>

    <?php if ($partners) { ?>
    <div class="egref__partners">
      <span class="egref__eyebrow">KURUMSAL İŞ ORTAKLARI</span>
      <h3>Birlikte çalıştığımız kurumsal markalar.</h3>
      <div class="egref__partners-grid">
        <?php foreach ($partners as $partner) { ?>
        <?php if ($partner['link']) { ?>
        <a class="egref__partner" href="<?php echo htmlspecialchars($partner['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
        <?php } else { ?>
        <div class="egref__partner">
        <?php } ?>
          <span class="egref__partner-logo"><img src="<?php echo htmlspecialchars($partner['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" decoding="async"></span>
          <?php if ($partner['name']) { ?><span class="egref__partner-name"><?php echo htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8'); ?></span><?php } ?>
        <?php if ($partner['link']) { ?></a><?php } else { ?></div><?php } ?>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
  </div>
</section>
