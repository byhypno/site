(function () {
  'use strict';

  function getHeaderOffset() {
    var header = document.querySelector('header, .eg-header, #header');
    if (!header) return 90;
    var rect = header.getBoundingClientRect();
    return Math.max(80, Math.round(rect.height + 16));
  }

  function scrollToTarget(id) {
    if (!id) return;
    var target = document.getElementById(id);
    if (!target) return;

    var offset = getHeaderOffset();
    var y = target.getBoundingClientRect().top + window.pageYOffset - offset;

    window.scrollTo({
      top: Math.max(0, y),
      behavior: 'smooth'
    });

    try {
      history.replaceState(null, '', '#' + id);
    } catch (e) {}
  }

  document.addEventListener('click', function (event) {
    var link = event.target.closest('.eg-tech-jump a[href^="#"]');
    if (!link) return;

    var href = link.getAttribute('href') || '';
    var id = href.replace(/^#/, '');
    if (!id) return;

    event.preventDefault();
    scrollToTarget(id);
  });

  document.addEventListener('DOMContentLoaded', function () {
    // If page opened directly with a fragment, compensate for sticky header.
    if (window.location.hash) {
      var id = window.location.hash.substring(1);
      setTimeout(function () {
        scrollToTarget(id);
      }, 100);
    }

    // Technical page CTA: use the existing global quote target if available.
    var cta = document.querySelector('.eg-tech-final-cta');
    if (cta && !cta.querySelector('.eg-tech-final-cta__actions')) {
      var actions = document.createElement('div');
      actions.className = 'eg-tech-final-cta__actions';

      var quote = document.createElement('a');
      quote.className = 'eg-btn eg-btn--primary';
      quote.href = '#eg-solutions';
      quote.textContent = 'Teklif / Bilgi Al';

      actions.appendChild(quote);
      cta.appendChild(actions);
    }

    // Resolve quote CTA to an existing form/section, falling back to contact route.
    document.addEventListener('click', function (event) {
      var quoteLink = event.target.closest('.eg-tech-final-cta__actions a[href="#eg-solutions"], .eg-side-cta a[href="#eg-solutions"]');
      if (!quoteLink) return;

      var targets = [
        document.getElementById('eg-solutions'),
        document.getElementById('eg-quote-form'),
        document.querySelector('.eg-quote-form'),
        document.querySelector('form[action*="contact"]')
      ];

      var target = null;
      for (var i = 0; i < targets.length; i++) {
        if (targets[i]) {
          target = targets[i];
          break;
        }
      }

      if (target) {
        event.preventDefault();
        var offset = getHeaderOffset();
        var y = target.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
      } else {
        quoteLink.setAttribute('href', 'index.php?route=information/contact');
      }
    });
  });
})();
