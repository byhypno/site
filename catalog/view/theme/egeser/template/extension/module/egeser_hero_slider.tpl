<div class="egeser-hero-slider-source"
     data-egeser-hero-slider
     data-interval="<?php echo (int)$interval; ?>"
     data-transition="<?php echo (int)$transition; ?>"
     data-pause-hover="<?php echo (int)$pause_hover; ?>"
     hidden aria-hidden="true">
  <?php foreach ($slides as $i => $slide) { ?>
    <span
      data-image="<?php echo htmlspecialchars($slide['image'], ENT_QUOTES, 'UTF-8'); ?>"
      data-alt="<?php echo htmlspecialchars($slide['alt'], ENT_QUOTES, 'UTF-8'); ?>"
      data-link="<?php echo htmlspecialchars($slide['link'], ENT_QUOTES, 'UTF-8'); ?>"></span>
  <?php } ?>
</div>

<script>
(function(){
  'use strict';

  function mountEgeserHeroSlider(){
    var source = document.querySelector('[data-egeser-hero-slider]:not([data-mounted="1"])');
    if (!source) return;

    var spans = source.querySelectorAll('span[data-image]');
    if (!spans.length) return;

    /* V1.1: Egeser V12 ana sayfa hero alanını doğrudan hedefler. */
    var target = document.querySelector('.eh12-hero-media');

    /* Tema class'ı değişirse eski metin tabanlı yöntem fallback olarak çalışır. */
    if (!target) {
      var marker = null;
      var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
      while (walker.nextNode()) {
        var txt = (walker.currentNode.nodeValue || '').replace(/\s+/g,' ').trim();
        if (txt.indexOf('Ana proje görseli') !== -1) {
          marker = walker.currentNode.parentElement;
          break;
        }
      }

      if (marker) {
        target = marker;
        for (var i = 0; i < 8 && target && target !== document.body; i++, target = target.parentElement) {
          var r = target.getBoundingClientRect();
          if (r.width > 360 && r.height > 300) break;
        }
      }
    }

    if (!target || target === document.body) return;

    source.setAttribute('data-mounted', '1');
    target.classList.add('egeser-hero-slider-host');

    var placeholder = target.querySelector('.eh12-hero-media__placeholder');
    if (placeholder) placeholder.style.display = 'none';

    var oldStage = target.querySelector('.egeser-hero-slider-stage');
    if (oldStage) oldStage.parentNode.removeChild(oldStage);

    var stage = document.createElement('div');
    stage.className = 'egeser-hero-slider-stage';
    stage.setAttribute('aria-label', 'Proje görselleri');

    var slides = [];
    Array.prototype.forEach.call(spans, function(span, idx){
      var link = span.getAttribute('data-link') || '';
      var wrap = link ? document.createElement('a') : document.createElement('div');
      wrap.className = 'egeser-hero-slide' + (idx === 0 ? ' is-active' : '');

      if (link) {
        wrap.setAttribute('href', link);
        wrap.setAttribute('aria-label', span.getAttribute('data-alt') || 'Proje detayını görüntüle');
      }

      var img = document.createElement('img');
      img.src = span.getAttribute('data-image');
      img.alt = span.getAttribute('data-alt') || 'Egeser Prefabrik proje görseli';
      img.width = 1080;
      img.height = 1080;
      img.decoding = 'async';
      img.loading = idx === 0 ? 'eager' : 'lazy';

      if (idx === 0) img.setAttribute('fetchpriority', 'high');

      wrap.appendChild(img);
      stage.appendChild(wrap);
      slides.push(wrap);
    });

    target.insertBefore(stage, target.firstChild);

    var transition = parseInt(source.getAttribute('data-transition'), 10) || 900;
    stage.style.setProperty('--egeser-hero-transition', transition + 'ms');

    if (slides.length < 2) return;

    var interval = parseInt(source.getAttribute('data-interval'), 10) || 5000;
    var current = 0;
    var timer = null;

    function next(){
      slides[current].classList.remove('is-active');
      current = (current + 1) % slides.length;
      slides[current].classList.add('is-active');
    }

    function start(){
      if (!timer) timer = setInterval(next, interval);
    }

    function stop(){
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
    }

    start();

    if (source.getAttribute('data-pause-hover') === '1') {
      target.addEventListener('mouseenter', stop);
      target.addEventListener('mouseleave', start);
    }

    document.addEventListener('visibilitychange', function(){
      document.hidden ? stop() : start();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountEgeserHeroSlider);
  } else {
    mountEgeserHeroSlider();
  }

  /* Layout/modül çıktısı geç oluşursa ikinci güvenli deneme. */
  window.setTimeout(mountEgeserHeroSlider, 400);
})();
</script>
