(function(){
  'use strict';
  var cfg = window.EGESER_VISUAL_MANAGER || {};
  var rules = Array.isArray(cfg.rules) ? cfg.rules : [];
  if (!rules.length) return;

  function imageUrl(path){
    path = String(path || '').replace(/^\/+/, '').replace(/^image\//, '');
    var base = window.location.origin.replace(/\/$/, '') + '/image/' + path;
    return base + (cfg.version ? ('?v=' + encodeURIComponent(cfg.version)) : '');
  }

  function textCandidates(target){
    var nodes = document.querySelectorAll('h1,h2,h3,h4,h5,h6,strong,span,p,a,div');
    var found = [];
    var needle = String(target || '').trim().toLocaleLowerCase('tr-TR');
    for (var i=0;i<nodes.length;i++) {
      var txt = (nodes[i].textContent || '').replace(/\s+/g,' ').trim().toLocaleLowerCase('tr-TR');
      if (txt && txt.indexOf(needle) !== -1) found.push(nodes[i]);
    }
    found.sort(function(a,b){ return (a.textContent||'').length - (b.textContent||'').length; });
    return found;
  }

  function findCard(start, needsImg){
    var el = start;
    for (var i=0;i<8 && el;i++, el=el.parentElement) {
      if (needsImg && el.querySelector && el.querySelector('img')) return el;
      if (!needsImg && el.getBoundingClientRect) {
        var r = el.getBoundingClientRect();
        var cls = String(el.className || '').toLowerCase();
        if (r.width > 220 && r.height > 140 && (cls.indexOf('card') !== -1 || cls.indexOf('item') !== -1 || cls.indexOf('box') !== -1)) return el;
      }
    }
    return start.parentElement || start;
  }

  function resolve(rule){
    if (rule.match_type === 'selector') {
      try { return Array.prototype.slice.call(document.querySelectorAll(rule.target)); }
      catch(e){ return []; }
    }
    var c = textCandidates(rule.target);
    if (!c.length) return [];
    return [findCard(c[0], rule.apply_type === 'img')];
  }

  function applyRule(rule){
    var matches = resolve(rule);
    if (!matches.length) return;
    var url = imageUrl(rule.image);
    matches.forEach(function(node){
      if (!node) return;
      if (rule.apply_type === 'background') {
        var target = node;
        target.style.backgroundImage = 'linear-gradient(rgba(10,10,10,.18),rgba(10,10,10,.28)),url("' + url.replace(/"/g,'\\"') + '")';
        target.style.backgroundSize = rule.fit || 'cover';
        target.style.backgroundPosition = rule.position || '50% 50%';
        target.style.backgroundRepeat = 'no-repeat';
        target.setAttribute('data-egeser-visual-managed','1');
        return;
      }

      var img = (node.tagName === 'IMG') ? node : (node.querySelector ? node.querySelector('img') : null);
      if (!img) return;
      img.src = url;
      img.removeAttribute('srcset');
      img.removeAttribute('sizes');
      if (img.hasAttribute('data-src')) img.setAttribute('data-src', url);
      if (rule.alt) img.alt = rule.alt;
      img.style.width = '100%';
      img.style.height = '100%';
      img.style.objectFit = rule.fit || 'cover';
      img.style.objectPosition = rule.position || '50% 50%';
      img.setAttribute('data-egeser-visual-managed','1');
    });
  }

  function applyAll(){ rules.forEach(applyRule); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', applyAll);
  else applyAll();
  window.addEventListener('load', applyAll);
  setTimeout(applyAll, 350);
  setTimeout(applyAll, 1100);
})();
