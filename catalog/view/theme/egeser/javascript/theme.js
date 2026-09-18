(function(){
'use strict';

function q(selector, context){ return (context || document).querySelector(selector); }
function qa(selector, context){ return (context || document).querySelectorAll(selector); }

var menuToggle=q('.eg-menu-toggle');
var mainNav=q('.eg-mainnav');
var megaToggle=q('.eg-mega-toggle');
var megaItem=q('.eg-nav-item--mega');

if(menuToggle && mainNav){
  menuToggle.addEventListener('click',function(){
    var open=mainNav.classList.toggle('is-open');
    menuToggle.setAttribute('aria-expanded',open?'true':'false');
  });
}

if(megaToggle && megaItem){
  megaToggle.addEventListener('click',function(event){
    if(window.innerWidth<=980){
      event.preventDefault();
      var open=megaItem.classList.toggle('is-open');
      megaToggle.setAttribute('aria-expanded',open?'true':'false');
    }
  });
}

document.addEventListener('keydown',function(event){
  if(event.key!=='Escape') return;
  if(mainNav) mainNav.classList.remove('is-open');
  if(menuToggle) menuToggle.setAttribute('aria-expanded','false');
  if(megaItem) megaItem.classList.remove('is-open');
  if(megaToggle) megaToggle.setAttribute('aria-expanded','false');
});

var EGESER_FIRST_PARTY_EVENTS=['whatsapp_click','phone_click','map_click','brochure_click','quote_form_start'];

function egeserSendBeacon(name, params){
  try{
    var body=new URLSearchParams();
    body.set('event_type', name);
    body.set('placement', (params && params.placement) || '');
    body.set('page_url', window.location.href);
    if(params && params.entity_type) body.set('entity_type', params.entity_type);
    if(params && params.entity_id) body.set('entity_id', params.entity_id);

    fetch('index.php?route=extension/module/egeser_pulse/save', {
      method:'POST',
      body:body,
      credentials:'same-origin',
      keepalive:true,
      headers:{'X-Requested-With':'XMLHttpRequest'}
    }).catch(function(){});
  }catch(error){}
}

function track(name, params){
  params=params || {};
  try{
    if(EGESER_FIRST_PARTY_EVENTS.indexOf(name)!==-1){
      egeserSendBeacon(name, params);
    }

    var cfg=window.EgeserTracking || {};

    if(typeof window.gtag==='function'){
      if(name==='lead_form_success'){
        window.gtag('event','generate_lead',params);
        if(cfg.google_ads_id && cfg.google_ads_lead_label){
          window.gtag('event','conversion',{send_to:cfg.google_ads_id+'/'+cfg.google_ads_lead_label});
        }
      }else if(name==='whatsapp_click'){
        window.gtag('event','contact',Object.assign({method:'whatsapp'},params));
      }else if(name==='phone_click'){
        window.gtag('event','contact',Object.assign({method:'phone'},params));
      }else{
        window.gtag('event',name,params);
      }
    }

    if(typeof window.fbq==='function'){
      if(name==='lead_form_success') window.fbq('track','Lead',params);
      else if(name==='whatsapp_click' || name==='phone_click') window.fbq('track','Contact',params);
      else window.fbq('trackCustom',name,params);
    }

    if(Array.isArray(window.dataLayer)){
      window.dataLayer.push({event:'egeser_'+name,egeser:params});
    }
  }catch(error){}
}

function marketingParams(){
  var params=new URLSearchParams(window.location.search);
  return {
    utm_source:params.get('utm_source') || '',
    utm_medium:params.get('utm_medium') || '',
    utm_campaign:params.get('utm_campaign') || ''
  };
}

function selectedCustomerType(form){
  var checked=q('input[name="customer_type"]:checked',form);
  if(checked) return checked.value;
  var select=q('select[name="customer_type"]',form);
  return select ? select.value : 'Bireysel';
}

function syncLeadCustomerType(form){
  if(!form) return;

  var corporate=selectedCustomerType(form)==='Kurumsal';

  Array.prototype.forEach.call(qa('.eg-corporate-field',form),function(field){
    field.hidden=!corporate;

    Array.prototype.forEach.call(qa('input,select,textarea',field),function(input){
      if(input.name==='company' || input.name==='email'){
        input.required=corporate;
        if(!corporate) input.value='';
      }
    });
  });
}

function bindLeadForm(form){
  var status=q('.eg-form-status',form);
  var utm=marketingParams();
  var startTracked=false;

  form.addEventListener('focusin',function(){
    if(startTracked) return;
    startTracked=true;
    var source=(q('input[name="source"]',form)||{}).value || '';
    track('quote_form_start',{placement:source || 'unknown'});
  });

  ['utm_source','utm_medium','utm_campaign'].forEach(function(key){
    var field=q('input[name="'+key+'"]',form);
    if(field) field.value=utm[key];
  });

  var pageUrl=q('input[name="page_url"]',form);
  if(pageUrl) pageUrl.value=window.location.href;

  Array.prototype.forEach.call(qa('input[name="customer_type"]',form),function(input){
    input.addEventListener('change',function(){ syncLeadCustomerType(form); });
  });

  var select=q('select[name="customer_type"]',form);
  if(select) select.addEventListener('change',function(){ syncLeadCustomerType(form); });

  syncLeadCustomerType(form);

  form.addEventListener('submit',function(event){
    event.preventDefault();

    if(!form.checkValidity()){
      form.reportValidity();
      return;
    }

    var submit=q('button[type="submit"]',form);
    if(submit) submit.disabled=true;
    if(status){
      status.className='eg-form-status is-loading';
      status.textContent='Gönderiliyor...';
    }

    fetch(form.action,{
      method:'POST',
      body:new FormData(form),
      credentials:'same-origin',
      headers:{'X-Requested-With':'XMLHttpRequest'}
    })
    .then(function(response){
      return response.json().then(function(data){
        return {ok:response.ok,data:data};
      });
    })
    .then(function(result){
      var data=result.data || {};
      if(data.success){
        if(status){
          status.className='eg-form-status is-success';
          status.textContent=data.message || 'Talebiniz alındı.';
        }

        track('lead_form_success',{
          customer_type:selectedCustomerType(form),
          source:(q('input[name="source"]',form)||{}).value || ''
        });

        form.reset();
        syncLeadCustomerType(form);

        if(data.csrf_token){
          var token=q('input[name="csrf_token"]',form);
          if(token) token.value=data.csrf_token;
        }
      }else{
        if(status){
          status.className='eg-form-status is-error';
          status.textContent=data.message || 'Talep gönderilemedi. Lütfen alanları kontrol edin.';
        }
      }
    })
    .catch(function(){
      if(status){
        status.className='eg-form-status is-error';
        status.textContent='Bağlantı hatası oluştu. WhatsApp veya telefon ile bize ulaşabilirsiniz.';
      }
    })
    .finally(function(){
      if(submit) submit.disabled=false;
    });
  });
}

Array.prototype.forEach.call(qa('.js-egeser-lead-form'),bindLeadForm);

Array.prototype.forEach.call(qa('[data-wa-message]'),function(link){
  link.addEventListener('click',function(){
    var phone=(link.getAttribute('data-wa-phone') || '').replace(/\D/g,'');
    var message=link.getAttribute('data-wa-message') || '';
    if(!phone) return;
    link.href='https://wa.me/'+phone+'?text='+encodeURIComponent(message);
    track('whatsapp_click',{
      placement:link.getAttribute('data-placement') || 'unknown',
      entity_type:link.getAttribute('data-entity-type') || '',
      entity_id:link.getAttribute('data-entity-id') || ''
    });
  });
});

Array.prototype.forEach.call(qa('a[href^="tel:"]'),function(link){
  link.addEventListener('click',function(){
    track('phone_click',{
      placement:link.getAttribute('data-placement') || 'unknown',
      entity_type:link.getAttribute('data-entity-type') || '',
      entity_id:link.getAttribute('data-entity-id') || ''
    });
  });
});

Array.prototype.forEach.call(qa('[data-eg-track]'),function(el){
  el.addEventListener('click',function(){
    track(el.getAttribute('data-eg-track'),{placement:el.getAttribute('data-placement') || 'unknown'});
  });
});

var filterGroups=document.querySelectorAll('[data-project-filter]');
var cards=document.querySelectorAll('.eg-project-archive-card[data-project-type]');
if(filterGroups.length && cards.length){
  var state={type:'all',location:'all'};

  function applyProjectFilters(){
    var visible=0;
    cards.forEach(function(card){
      var typeOk=state.type==='all' || card.getAttribute('data-project-type')===state.type;
      var locationOk=state.location==='all' || card.getAttribute('data-project-location')===state.location;
      card.hidden=!(typeOk && locationOk);
      if(typeOk && locationOk) visible++;
    });
    var empty=document.querySelector('.eg-project-filter-empty');
    if(empty) empty.hidden=visible!==0;
  }

  filterGroups.forEach(function(button){
    button.addEventListener('click',function(){
      var key=button.getAttribute('data-project-filter');
      var value=button.getAttribute('data-value') || 'all';
      state[key]=value;
      document.querySelectorAll('[data-project-filter="'+key+'"]').forEach(function(item){
        item.classList.toggle('is-active',item===button);
      });
      applyProjectFilters();
    });
  });
}
}());
