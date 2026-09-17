(function () {
  'use strict';

  function normalizeText(value) {
    return (value || '')
      .replace(/\s+/g, ' ')
      .replace(/İ/g, 'I')
      .replace(/ı/g, 'i')
      .trim()
      .toLowerCase();
  }

  function applyEgeserMenuLinkFix() {
    var target = 'tum prefabrik ev modelleri';
    var links = document.querySelectorAll('a');

    for (var i = 0; i < links.length; i++) {
      var link = links[i];
      var text = normalizeText(link.textContent);

      if (text.indexOf(target) !== -1) {
        link.setAttribute('href', '/prefabrik-ev-modelleri');
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyEgeserMenuLinkFix);
  } else {
    applyEgeserMenuLinkFix();
  }

  document.addEventListener('click', function (event) {
    var node = event.target;
    while (node && node.tagName !== 'A') {
      node = node.parentNode;
    }

    if (!node) return;

    if (normalizeText(node.textContent).indexOf('tum prefabrik ev modelleri') !== -1) {
      node.setAttribute('href', '/prefabrik-ev-modelleri');
    }
  }, true);
})();
