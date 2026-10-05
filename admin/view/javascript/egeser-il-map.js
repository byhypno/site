(function () {
    'use strict';

    function initMap(container) {
        var dataEl = document.getElementById(container.getAttribute('data-counts-id'));
        var counts = {};
        try {
            counts = JSON.parse(dataEl ? dataEl.textContent : '{}');
        } catch (e) {
            counts = {};
        }

        var svg = container.querySelector('svg');
        var fullViewBox = svg ? svg.getAttribute('viewBox') : null;
        var cropAttr = container.getAttribute('data-crop-slugs') || '';
        var cropSlugs = cropAttr ? cropAttr.split(',') : [];

        function setView(mode) {
            if (!svg) return;

            if (mode === 'crop' && cropSlugs.length) {
                var minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
                var found = false;

                cropSlugs.forEach(function (slug) {
                    var el = svg.querySelector('#' + slug);
                    if (!el || typeof el.getBBox !== 'function') return;
                    var box = el.getBBox();
                    if (!box || (box.width === 0 && box.height === 0)) return;
                    found = true;
                    minX = Math.min(minX, box.x);
                    minY = Math.min(minY, box.y);
                    maxX = Math.max(maxX, box.x + box.width);
                    maxY = Math.max(maxY, box.y + box.height);
                });

                if (found) {
                    var pad = Math.max(maxX - minX, maxY - minY) * 0.12;
                    svg.setAttribute('viewBox', (minX - pad) + ' ' + (minY - pad) + ' ' + (maxX - minX + pad * 2) + ' ' + (maxY - minY + pad * 2));
                    return;
                }
            }

            if (fullViewBox) {
                svg.setAttribute('viewBox', fullViewBox);
            }
        }

        var toggles = container.parentNode.querySelectorAll('[data-map-view]');
        for (var i = 0; i < toggles.length; i++) {
            toggles[i].addEventListener('click', function (ev) {
                ev.preventDefault();
                var mode = this.getAttribute('data-map-view');
                for (var j = 0; j < toggles.length; j++) {
                    toggles[j].classList.remove('active');
                }
                this.classList.add('active');
                setView(mode);
            });
        }

        var defaultToggle = container.parentNode.querySelector('[data-map-view].active');
        setView(defaultToggle ? defaultToggle.getAttribute('data-map-view') : (cropSlugs.length ? 'crop' : 'full'));

        var tooltip = document.createElement('div');
        tooltip.className = 'egeser-il-tooltip';
        document.body.appendChild(tooltip);

        container.addEventListener('mousemove', function (ev) {
            var group = ev.target.closest('[data-city-code]');
            if (!group) {
                tooltip.style.display = 'none';
                return;
            }

            var code = parseInt(group.getAttribute('data-city-code'), 10);
            var info = counts[code];
            var name = group.getAttribute('data-city-name') || '';
            var count = info ? info.count : 0;

            tooltip.innerHTML = '<strong>' + name + '</strong>: ' + count + ' ziyaretçi';
            tooltip.style.display = 'block';
            tooltip.style.left = (ev.clientX + 14) + 'px';
            tooltip.style.top = (ev.clientY + 14) + 'px';
        });

        container.addEventListener('mouseleave', function () {
            tooltip.style.display = 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var maps = document.querySelectorAll('.egeser-il-map-wrap');
        for (var i = 0; i < maps.length; i++) {
            initMap(maps[i]);
        }
    });
})();
