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
      <?php foreach ($projects as $index => $project) { ?>
      <article class="egref__card">
        <button type="button" class="egref__media" data-egref-open="<?php echo (int) $index; ?>" aria-label="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?> - büyük görseli aç">
          <?php if ($project['image']) { ?>
            <img src="<?php echo htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8'); ?>"
                 alt="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>"
                 width="640" height="640" loading="lazy" decoding="async">
          <?php } else { ?>
            <div class="egref__placeholder">Proje görseli eklenecek</div>
          <?php } ?>

          <?php if ($project['type']) { ?>
            <span class="egref__badge"><?php echo htmlspecialchars($project['type'], ENT_QUOTES, 'UTF-8'); ?></span>
          <?php } ?>
          <?php if ($project['image']) { ?>
            <span class="egref__zoom" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path><path d="M11 8v6M8 11h6"></path></svg>
            </span>
          <?php } ?>
        </button>

        <div class="egref__body">
          <?php if ($project['eyebrow']) { ?>
            <small><?php echo htmlspecialchars($project['eyebrow'], ENT_QUOTES, 'UTF-8'); ?></small>
          <?php } ?>

          <h3><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h3>

          <?php if ($project['location'] || $project['size']) { ?>
          <div class="egref__meta">
            <?php if ($project['location']) { ?><span><?php echo htmlspecialchars($project['location'], ENT_QUOTES, 'UTF-8'); ?></span><?php } ?>
            <?php if ($project['size']) { ?><span><?php echo htmlspecialchars($project['size'], ENT_QUOTES, 'UTF-8'); ?></span><?php } ?>
          </div>
          <?php } ?>

          <?php if ($project['description']) { ?>
            <p class="egref__sr-desc"><?php echo htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?></p>
          <?php } ?>
        </div>
      </article>
      <?php } ?>
    </div>

    <div class="egref__lightbox" id="egref-lightbox" aria-hidden="true">
      <div class="egref__lightbox-backdrop" data-egref-close></div>
      <div class="egref__lightbox-dialog" role="dialog" aria-modal="true" aria-label="Proje detayı">
        <button type="button" class="egref__lightbox-close" data-egref-close aria-label="Kapat">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
        </button>
        <button type="button" class="egref__lightbox-nav egref__lightbox-nav--prev" data-egref-prev aria-label="Önceki proje">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
        </button>
        <button type="button" class="egref__lightbox-nav egref__lightbox-nav--next" data-egref-next aria-label="Sonraki proje">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"></path></svg>
        </button>
        <div class="egref__lightbox-media">
          <img id="egref-lightbox-img" src="" alt="">
          <span class="egref__lightbox-badge" id="egref-lightbox-badge"></span>
        </div>
        <div class="egref__lightbox-info">
          <small id="egref-lightbox-eyebrow"></small>
          <h3 id="egref-lightbox-title"></h3>
          <p id="egref-lightbox-desc"></p>
          <div class="egref__meta" id="egref-lightbox-meta"></div>
          <a class="egref__link" id="egref-lightbox-link" href="#" target="_blank" rel="noopener">Projeyi İncele <span aria-hidden="true">→</span></a>
        </div>
      </div>
    </div>

    <script>
    (function(){
      'use strict';
      var DATA = <?php
        $lightbox_data = array();
        foreach ($projects as $project) {
          $lightbox_data[] = array(
            'image' => $project['image_large'] ? $project['image_large'] : $project['image'],
            'title' => $project['title'],
            'eyebrow' => $project['eyebrow'],
            'type' => $project['type'],
            'description' => $project['description'],
            'location' => $project['location'],
            'size' => $project['size'],
            'link' => $project['link']
          );
        }
        echo json_encode($lightbox_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
      ?>;
      var box = document.getElementById('egref-lightbox');
      if (!box || !DATA.length) { return; }

      var imgEl = document.getElementById('egref-lightbox-img');
      var badgeEl = document.getElementById('egref-lightbox-badge');
      var eyebrowEl = document.getElementById('egref-lightbox-eyebrow');
      var titleEl = document.getElementById('egref-lightbox-title');
      var descEl = document.getElementById('egref-lightbox-desc');
      var metaEl = document.getElementById('egref-lightbox-meta');
      var linkEl = document.getElementById('egref-lightbox-link');
      var current = 0;

      function render(i){
        var d = DATA[i];
        if (!d) { return; }
        current = i;
        imgEl.src = d.image;
        imgEl.alt = d.title;
        badgeEl.textContent = d.type || '';
        badgeEl.style.display = d.type ? '' : 'none';
        eyebrowEl.textContent = d.eyebrow || '';
        eyebrowEl.style.display = d.eyebrow ? '' : 'none';
        titleEl.textContent = d.title || '';
        descEl.textContent = d.description || '';
        descEl.style.display = d.description ? '' : 'none';

        metaEl.innerHTML = '';
        if (d.location) { var s1 = document.createElement('span'); s1.textContent = d.location; metaEl.appendChild(s1); }
        if (d.size) { var s2 = document.createElement('span'); s2.textContent = d.size; metaEl.appendChild(s2); }
        metaEl.style.display = (d.location || d.size) ? '' : 'none';

        if (d.link) { linkEl.href = d.link; linkEl.style.display = ''; } else { linkEl.style.display = 'none'; }
      }

      function open(i){
        render(i);
        box.classList.add('is-open');
        box.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
      }
      function close(){
        box.classList.remove('is-open');
        box.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      }
      function step(delta){
        var next = (current + delta + DATA.length) % DATA.length;
        render(next);
      }

      document.querySelectorAll('[data-egref-open]').forEach(function(btn){
        btn.addEventListener('click', function(){
          open(parseInt(btn.getAttribute('data-egref-open'), 10) || 0);
        });
      });
      box.querySelectorAll('[data-egref-close]').forEach(function(el){
        el.addEventListener('click', close);
      });
      box.querySelector('[data-egref-prev]').addEventListener('click', function(){ step(-1); });
      box.querySelector('[data-egref-next]').addEventListener('click', function(){ step(1); });

      document.addEventListener('keydown', function(e){
        if (!box.classList.contains('is-open')) { return; }
        if (e.key === 'Escape') { close(); }
        if (e.key === 'ArrowLeft') { step(-1); }
        if (e.key === 'ArrowRight') { step(1); }
      });
    })();
    </script>
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
          <?php if ($partner['description']) { ?><span class="egref__partner-desc"><?php echo htmlspecialchars($partner['description'], ENT_QUOTES, 'UTF-8'); ?></span><?php } ?>
        <?php if ($partner['link']) { ?></a><?php } else { ?></div><?php } ?>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
  </div>
</section>
