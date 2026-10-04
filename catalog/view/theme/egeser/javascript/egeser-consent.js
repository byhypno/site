(function () {
  'use strict';

  var COOKIE_NAME = 'egeser_consent';
  var COOKIE_DAYS = 180;

  function getCookie(name) {
    var match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
    return match ? decodeURIComponent(match[1]) : null;
  }

  function setCookie(name, value) {
    var d = new Date();
    d.setTime(d.getTime() + COOKIE_DAYS * 24 * 60 * 60 * 1000);
    document.cookie = name + '=' + encodeURIComponent(value) + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax' + (location.protocol === 'https:' ? ';Secure' : '');
  }

  function activateGatedScripts() {
    var nodes = document.querySelectorAll('script[data-eg-consent="analytics"]');
    for (var i = 0; i < nodes.length; i++) {
      var old = nodes[i];
      var fresh = document.createElement('script');
      for (var a = 0; a < old.attributes.length; a++) {
        var attr = old.attributes[a];
        if (attr.name === 'type' || attr.name === 'data-eg-consent') { continue; }
        if (attr.name === 'data-eg-src') {
          fresh.src = attr.value;
          continue;
        }
        fresh.setAttribute(attr.name, attr.value);
      }
      if (old.textContent) { fresh.textContent = old.textContent; }
      old.parentNode.replaceChild(fresh, old);
    }
  }

  function hideBanner() {
    var el = document.getElementById('eg-consent-banner');
    if (el) { el.classList.remove('is-visible'); }
  }

  function showBanner() {
    var el = document.getElementById('eg-consent-banner');
    if (el) { el.classList.add('is-visible'); }
  }

  function init() {
    var choice = getCookie(COOKIE_NAME);

    if (choice === 'granted') {
      activateGatedScripts();
    } else if (choice !== 'denied') {
      showBanner();
    }

    var acceptBtn = document.getElementById('eg-consent-accept');
    var rejectBtn = document.getElementById('eg-consent-reject');

    if (acceptBtn) {
      acceptBtn.addEventListener('click', function () {
        setCookie(COOKIE_NAME, 'granted');
        activateGatedScripts();
        hideBanner();
      });
    }
    if (rejectBtn) {
      rejectBtn.addEventListener('click', function () {
        setCookie(COOKIE_NAME, 'denied');
        hideBanner();
      });
    }

    var reopenLinks = document.querySelectorAll('[data-eg-consent-reopen]');
    for (var r = 0; r < reopenLinks.length; r++) {
      reopenLinks[r].addEventListener('click', function (e) {
        e.preventDefault();
        showBanner();
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
