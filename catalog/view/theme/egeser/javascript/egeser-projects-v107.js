(function(){
  'use strict';
  function offset(){var h=document.querySelector('header,.eg-header,#header');return h?Math.max(80,h.getBoundingClientRect().height+16):90}
  function go(id){var el=document.getElementById(id);if(!el)return;var y=el.getBoundingClientRect().top+window.pageYOffset-offset();window.scrollTo({top:Math.max(0,y),behavior:'smooth'});try{history.replaceState(null,'','#'+id)}catch(e){}}
  document.addEventListener('click',function(e){
    var a=e.target.closest('.eg-projects-jump a[href^="#"]');
    if(!a)return;
    var id=(a.getAttribute('href')||'').replace(/^#/,'');
    if(!id)return;
    e.preventDefault();go(id);
  });
})();
