(function () {
  'use strict';

  var mediaSelectorList = [
    '.eg11-main-media',
    '.eg11-thumb',
    '.eg-product-card__image',
    '.eg11-related-card > a',
    '.eh12-product__media',
    '.eh12-project__media',
    '.eg-project-card__image',
    '.eg-project-archive-card__media',
    '.eg-project-gallery__item',
    '.egref__media',
    '.eg-blog-card__image'
  ];
  var mediaSelectors = mediaSelectorList.join(',');
  var mediaImageSelectors = mediaSelectorList.map(function (selector) {
    return selector + ' img';
  }).join(',');

  function markBroken(image) {
    if (!image || image.getAttribute('data-eg-image-checked') === 'broken') return;
    image.setAttribute('data-eg-image-checked', 'broken');

    var media = image.closest ? image.closest(mediaSelectors) : image.parentNode;
    if (!media) {
      image.style.display = 'none';
      return;
    }

    if (media.classList && media.classList.contains('eg11-thumb')) {
      if (media.parentNode) media.parentNode.removeChild(media);
      return;
    }

    if (media.classList) media.classList.add('eg-image-missing');
  }

  document.addEventListener('error', function (event) {
    var target = event.target;
    if (target && target.tagName === 'IMG') markBroken(target);
  }, true);

  function inspectLoadedImages() {
    var images = document.querySelectorAll(mediaImageSelectors);
    Array.prototype.forEach.call(images, function (image) {
      if (image.complete && !image.naturalWidth) markBroken(image);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inspectLoadedImages);
  } else {
    inspectLoadedImages();
  }
}());
