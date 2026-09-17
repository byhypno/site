<?php
$eg_form_context = isset($eg_form_context) ? $eg_form_context : 'generic';
$eg_form_product_id = isset($eg_form_product_id) ? (int)$eg_form_product_id : 0;
$eg_form_product_name = isset($eg_form_product_name) ? $eg_form_product_name : '';
$eg_form_source = isset($eg_form_source) ? $eg_form_source : '';
$eg_form_title = isset($eg_form_title) ? $eg_form_title : 'Projeniz İçin Teklif Alın';
$eg_form_default_customer_type = isset($eg_form_default_customer_type) ? trim($eg_form_default_customer_type) : 'Bireysel';
$eg_form_is_corporate = ($eg_form_default_customer_type === 'Kurumsal');
?>
<section class="eg-lead-box" aria-labelledby="eg-lead-title-<?php echo htmlspecialchars($eg_form_context, ENT_QUOTES, 'UTF-8'); ?>">
  <div class="eg-lead-box__intro">
    <span class="eg-section__eyebrow">TEKLİF</span>
    <h2 id="eg-lead-title-<?php echo htmlspecialchars($eg_form_context, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($eg_form_title, ENT_QUOTES, 'UTF-8'); ?></h2>
    <p>Bireysel veya kurumsal ihtiyacınızı iletin. Satış ekibimiz proje bilgilerinize göre sizinle iletişime geçsin.</p>
  </div>

  <form class="eg-lead-form js-egeser-lead-form" method="post" action="index.php?route=extension/module/egeser_lead/submit" novalidate>
    <input type="hidden" name="csrf_token" value="<?php echo isset($egeser_csrf_token) ? htmlspecialchars($egeser_csrf_token, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <input type="hidden" name="product_id" value="<?php echo $eg_form_product_id; ?>">
    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($eg_form_product_name, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="source" value="<?php echo htmlspecialchars($eg_form_source, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="page_url" value="<?php echo isset($egeser_current_url) ? htmlspecialchars($egeser_current_url, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <input type="hidden" name="utm_source" value="">
    <input type="hidden" name="utm_medium" value="">
    <input type="hidden" name="utm_campaign" value="">
    <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="eg-hp-field" aria-hidden="true">

    <div class="eg-form-grid">
      <div class="eg-field eg-field--full">
        <span class="eg-field__label">Müşteri Tipi</span>
        <div class="eg-choice-row" role="radiogroup" aria-label="Müşteri tipi">
          <label class="eg-choice"><input type="radio" name="customer_type" value="Bireysel"<?php echo !$eg_form_is_corporate ? ' checked' : ''; ?>><span>Bireysel</span></label>
          <label class="eg-choice"><input type="radio" name="customer_type" value="Kurumsal"<?php echo $eg_form_is_corporate ? ' checked' : ''; ?>><span>Kurumsal</span></label>
        </div>
      </div>

      <div class="eg-field eg-corporate-field"<?php echo $eg_form_is_corporate ? '' : ' hidden'; ?>>
        <label for="eg-company-<?php echo $eg_form_context; ?>">Firma Ünvanı *</label>
        <input id="eg-company-<?php echo $eg_form_context; ?>" type="text" name="company" maxlength="150" autocomplete="organization"<?php echo $eg_form_is_corporate ? ' required' : ''; ?>>
      </div>

      <div class="eg-field">
        <label for="eg-name-<?php echo $eg_form_context; ?>">Ad Soyad *</label>
        <input id="eg-name-<?php echo $eg_form_context; ?>" type="text" name="name" maxlength="80" autocomplete="name" required>
      </div>

      <div class="eg-field">
        <label for="eg-phone-<?php echo $eg_form_context; ?>">Telefon *</label>
        <input id="eg-phone-<?php echo $eg_form_context; ?>" type="tel" name="phone" maxlength="30" autocomplete="tel" inputmode="tel" required>
      </div>

      <div class="eg-field eg-corporate-field"<?php echo $eg_form_is_corporate ? '' : ' hidden'; ?>>
        <label for="eg-email-<?php echo $eg_form_context; ?>">Kurumsal E-posta *</label>
        <input id="eg-email-<?php echo $eg_form_context; ?>" type="email" name="email" maxlength="120" autocomplete="email"<?php echo $eg_form_is_corporate ? ' required' : ''; ?>>
      </div>

      <div class="eg-field">
        <label for="eg-project-<?php echo $eg_form_context; ?>">Yapı Türü</label>
        <select id="eg-project-<?php echo $eg_form_context; ?>" name="project_type">
          <option value="">Seçiniz</option>
          <option value="Prefabrik Ev">Prefabrik Ev</option>
          <option value="Ofis ve Yönetim Binası">Ofis ve Yönetim Binası</option>
          <option value="Yatakhane">Yatakhane</option>
          <option value="Yemekhane">Yemekhane</option>
          <option value="Şantiye Yapısı">Şantiye Yapısı</option>
          <option value="Sosyal Tesis">Sosyal Tesis</option>
          <option value="Özel Proje">Özel Proje</option>
        </select>
      </div>

      <div class="eg-field">
        <label for="eg-location-<?php echo $eg_form_context; ?>">Kurulum / Proje Yeri</label>
        <input id="eg-location-<?php echo $eg_form_context; ?>" type="text" name="location" maxlength="120" autocomplete="address-level1">
      </div>

      <div class="eg-field">
        <label for="eg-area-<?php echo $eg_form_context; ?>">Yaklaşık m²</label>
        <input id="eg-area-<?php echo $eg_form_context; ?>" type="text" name="area" maxlength="40" inputmode="decimal">
      </div>

      <div class="eg-field eg-field--full">
        <label for="eg-message-<?php echo $eg_form_context; ?>">Mesaj</label>
        <textarea id="eg-message-<?php echo $eg_form_context; ?>" name="message" rows="5" maxlength="2000"></textarea>
      </div>

      <div class="eg-field eg-field--full">
        <label class="eg-consent">
          <input type="checkbox" name="consent" value="1" required>
          <span>İletişim bilgilerimin teklif talebimin yanıtlanması amacıyla işlenmesini kabul ediyorum. *</span>
        </label>
      </div>
    </div>

    <div class="eg-form-actions">
      <button type="submit" class="eg-btn eg-btn--primary">Teklif Talebi Gönder</button>
      <span class="eg-form-status" role="status" aria-live="polite"></span>
    </div>
  </form>
</section>

<script id="egeser-lead-security-final">
(function(){
  'use strict';

  var script = document.currentScript;
  var section = script ? script.previousElementSibling : null;
  var form = section && section.querySelector ? section.querySelector('.js-egeser-lead-form') : null;
  if (!form) {
    form = document.querySelector('.js-egeser-lead-form');
  }
  if (!form || form.getAttribute('data-egeser-final-security') === '1') return;

  form.setAttribute('data-egeser-final-security','1');

  var token = form.querySelector('input[name="csrf_token"]');
  var button = form.querySelector('button[type="submit"]');
  var status = form.querySelector('.eg-form-status');

  if (!token) return;

  function busy(state) {
    if (button) {
      button.disabled = !!state;
      button.setAttribute('aria-busy', state ? 'true' : 'false');
    }
  }

  function nonce() {
    busy(true);

    return fetch('index.php?route=extension/module/egeser_lead/nonce', {
      method: 'GET',
      credentials: 'same-origin',
      cache: 'no-store',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(function(r){
      return r.json().then(function(data){
        if (!r.ok || !data || !data.success || !data.csrf_token) {
          throw new Error('nonce');
        }
        return data.csrf_token;
      });
    })
    .then(function(value){
      token.value = value;
      form.setAttribute('data-egeser-nonce-ready','1');

      if (status && (
        status.textContent.indexOf('Oturum doğrulaması') !== -1 ||
        status.textContent.indexOf('güvenlik doğrulaması') !== -1 ||
        status.textContent.indexOf('güvenlik anahtarı') !== -1
      )) {
        status.className = 'eg-form-status';
        status.textContent = '';
      }

      return value;
    })
    .catch(function(){
      token.value = '';
      form.setAttribute('data-egeser-nonce-ready','0');

      if (status) {
        status.className = 'eg-form-status is-error';
        status.textContent = 'Form güvenlik anahtarı hazırlanamadı. Sayfayı yenileyip tekrar deneyin.';
      }

      return '';
    })
    .then(function(value){
      busy(false);
      return value;
    });
  }

  nonce();

  form.addEventListener('submit', function(event){
    if (!token.value || form.getAttribute('data-egeser-nonce-ready') !== '1') {
      event.preventDefault();
      event.stopImmediatePropagation();

      nonce().then(function(value){
        if (!value) return;

        if (typeof form.requestSubmit === 'function') {
          form.requestSubmit(button || undefined);
        } else if (button) {
          button.click();
        }
      });
    }
  }, true);
})();
</script>

