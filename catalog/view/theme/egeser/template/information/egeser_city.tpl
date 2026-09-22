<?php echo $header; ?>
<style>
.ecity{--red:#ed1c24;--ink:#1d1917;--gold:#efa825;--soft:#f8f5f1;--line:#eaded4;color:#4d4a48;font-family:inherit;background:#fff}
.ecity *{box-sizing:border-box}.ecity a{text-decoration:none}.ecity-wrap{width:min(1240px,calc(100% - 40px));margin:0 auto}
.ecity-bc{display:flex;align-items:center;gap:9px;flex-wrap:wrap;padding:18px 0;color:#777;font-size:13px}.ecity-bc a{color:#777}.ecity-bc a:hover{color:var(--red)}.ecity-bc i{font-style:normal;color:#bbb}
.ecity-hero{display:grid;grid-template-columns:minmax(0,1.08fr) minmax(360px,.92fr);min-height:470px;border:1px solid var(--line);border-radius:28px;overflow:hidden;background:#fff}
.ecity-hero__copy{padding:56px}.ecity-kicker{display:flex;align-items:center;gap:11px;color:var(--red);font-weight:800;font-size:12px;letter-spacing:.14em}.ecity-kicker:before{content:"";width:30px;height:2px;background:var(--red)}
.ecity h1{margin:16px 0 18px;color:var(--ink);font-size:clamp(38px,4.2vw,62px);line-height:1.03;letter-spacing:-.045em}.ecity-hero__copy>p{max-width:680px;margin:0;color:#68625e;font-size:18px;line-height:1.72}
.ecity-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:28px}.ecity-btn{display:inline-flex;align-items:center;justify-content:center;min-height:50px;padding:0 22px;border-radius:12px;font-size:14px;font-weight:800;transition:.2s}.ecity-btn--red{background:var(--red);color:#fff;box-shadow:0 12px 28px rgba(237,28,36,.18)}.ecity-btn--red:hover{background:#cb151c;color:#fff;transform:translateY(-2px)}.ecity-btn--line{border:1px solid var(--red);color:var(--red);background:#fff}.ecity-btn--line:hover{background:#fff4f4;color:#c8141b}
.ecity-hero__panel{position:relative;display:flex;flex-direction:column;justify-content:space-between;padding:52px;background:linear-gradient(145deg,#171513,#281b17);color:#fff;overflow:hidden}.ecity-hero__panel:after{content:"";position:absolute;right:-95px;bottom:-95px;width:280px;height:280px;border-radius:50%;border:60px solid rgba(239,168,37,.1)}.ecity-hero__panel>*{position:relative;z-index:1}.ecity-hero__panel small{color:var(--gold);font-weight:800;letter-spacing:.13em}.ecity-hero__panel h2{margin:15px 0 18px;color:#fff;font-size:31px;line-height:1.14}.ecity-hero__panel p{margin:0;color:#ded8d4;line-height:1.75}.ecity-hero__facts{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:32px}.ecity-hero__facts span{padding:14px;border:1px solid rgba(255,255,255,.14);border-radius:12px;color:#fff;font-size:12px;font-weight:700}
.ecity-section{padding:78px 0}.ecity-section--soft{background:var(--soft);margin-top:78px}.ecity-heading{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(280px,.75fr);gap:50px;align-items:end;margin-bottom:30px}.ecity-label{display:block;color:var(--red);font-size:12px;font-weight:900;letter-spacing:.14em}.ecity-heading h2{margin:10px 0 0;color:var(--ink);font-size:clamp(30px,3vw,45px);line-height:1.12;letter-spacing:-.035em}.ecity-heading p{margin:0;color:#6e6864;line-height:1.7}
.ecity-factor-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}.ecity-card{padding:28px;border:1px solid var(--line);border-radius:20px;background:#fff}.ecity-card__no{display:grid;place-items:center;width:38px;height:38px;margin-bottom:30px;border-radius:50%;background:#fff3e0;color:#c77500;font-size:12px;font-weight:900}.ecity-card h3{margin:0 0 10px;color:var(--ink);font-size:21px}.ecity-card p{margin:0;color:#6d6762;line-height:1.68}
.ecity-split{display:grid;grid-template-columns:1fr 1fr;gap:22px}.ecity-dark{padding:38px;border-radius:24px;background:#1e1917;color:#fff}.ecity-dark .ecity-label{color:var(--gold)}.ecity-dark h2,.ecity-light h2{margin:10px 0 20px;font-size:30px;line-height:1.15}.ecity-dark h2{color:#fff}.ecity-dark p{color:#ddd5d1;line-height:1.75}.ecity-use-list{display:grid;gap:10px;margin-top:26px}.ecity-use-list span{display:flex;gap:10px;align-items:center;padding:13px 15px;border:1px solid rgba(255,255,255,.12);border-radius:11px;font-weight:700}.ecity-use-list span:before{content:"✓";color:var(--gold)}
.ecity-light{padding:38px;border:1px solid var(--line);border-radius:24px;background:#fff}.ecity-light h2{color:var(--ink)}.ecity-light>p{line-height:1.75}.ecity-districts{display:flex;flex-wrap:wrap;gap:9px;margin-top:24px}.ecity-districts span{padding:10px 13px;border-radius:999px;background:var(--soft);border:1px solid var(--line);font-size:13px;font-weight:750;color:#4c4744}
.ecity-process{display:grid;grid-template-columns:repeat(4,1fr);border:1px solid var(--line);border-radius:22px;overflow:hidden}.ecity-step{padding:26px;background:#fff;border-right:1px solid var(--line)}.ecity-step:last-child{border:0}.ecity-step b{color:var(--red);font-size:12px;letter-spacing:.1em}.ecity-step h3{margin:13px 0 8px;color:var(--ink);font-size:18px}.ecity-step p{margin:0;font-size:14px;line-height:1.6}
.ecity-note{display:grid;grid-template-columns:auto 1fr;gap:18px;align-items:start;margin-top:22px;padding:22px;border-left:4px solid var(--gold);border-radius:12px;background:#fff8ea}.ecity-note strong{color:#7b4a00}.ecity-note p{margin:0;line-height:1.68}
.ecity-faq{display:grid;gap:11px}.ecity-faq details{border:1px solid var(--line);border-radius:15px;background:#fff}.ecity-faq summary{position:relative;padding:20px 54px 20px 20px;cursor:pointer;color:var(--ink);font-weight:800;list-style:none}.ecity-faq summary::-webkit-details-marker{display:none}.ecity-faq summary:after{content:"+";position:absolute;right:20px;top:15px;color:var(--gold);font-size:25px}.ecity-faq details[open] summary:after{content:"−"}.ecity-faq p{margin:0;padding:0 20px 20px;color:#67615d;line-height:1.7}
.ecity-related{display:flex;gap:9px;flex-wrap:wrap;margin-top:28px}.ecity-related a{padding:10px 14px;border:1px solid var(--line);border-radius:999px;color:#514b47;font-size:13px;font-weight:700}.ecity-related a:hover{border-color:var(--red);color:var(--red)}
.ecity-cta{display:flex;align-items:center;justify-content:space-between;gap:28px;margin:0 auto 78px;padding:38px;border-radius:24px;background:linear-gradient(135deg,#171513,#281b17);color:#fff}.ecity-cta h2{margin:7px 0 8px;color:#fff;font-size:32px}.ecity-cta p{margin:0;color:#d8d2ce}.ecity-cta .ecity-actions{margin:0;flex-shrink:0}.ecity-cta .ecity-btn--line{border-color:#fff;color:#fff;background:transparent}.ecity-cta .ecity-btn--line:hover{background:#fff;color:var(--ink)}
@media(max-width:900px){.ecity-hero{grid-template-columns:1fr}.ecity-hero__panel{min-height:350px}.ecity-heading{grid-template-columns:1fr;gap:14px}.ecity-factor-grid{grid-template-columns:1fr}.ecity-process{grid-template-columns:1fr 1fr}.ecity-step:nth-child(2){border-right:0}.ecity-step:nth-child(-n+2){border-bottom:1px solid var(--line)}.ecity-split{grid-template-columns:1fr}.ecity-cta{align-items:flex-start;flex-direction:column}.ecity-cta .ecity-actions{margin-top:8px}}
@media(max-width:620px){.ecity-wrap{width:min(100% - 28px,1240px)}.ecity-bc{padding:14px 0;font-size:12px}.ecity-hero{border-radius:20px}.ecity-hero__copy,.ecity-hero__panel{padding:30px 24px}.ecity h1{font-size:38px}.ecity-hero__copy>p{font-size:16px}.ecity-hero__panel h2{font-size:26px}.ecity-section{padding:54px 0}.ecity-section--soft{margin-top:54px}.ecity-heading h2{font-size:32px}.ecity-process{grid-template-columns:1fr}.ecity-step{border-right:0;border-bottom:1px solid var(--line)!important}.ecity-step:last-child{border-bottom:0!important}.ecity-dark,.ecity-light{padding:28px 22px}.ecity-cta{margin-bottom:94px;padding:29px 23px}.ecity-cta h2{font-size:27px}.ecity-actions,.ecity-cta .ecity-actions{width:100%}.ecity-btn{flex:1;padding:0 14px}.ecity-hero__facts{grid-template-columns:1fr}}
</style>

<main class="ecity">
  <div class="ecity-wrap">
    <nav class="ecity-bc" aria-label="Sayfa yolu">
      <?php foreach ($breadcrumbs as $index => $breadcrumb) { ?>
        <?php if ($index > 0) { ?><i>/</i><?php } ?>
        <?php if ($index + 1 < count($breadcrumbs)) { ?>
          <a href="<?php echo htmlspecialchars($breadcrumb['href'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($breadcrumb['text'], ENT_QUOTES, 'UTF-8'); ?></a>
        <?php } else { ?>
          <span aria-current="page"><?php echo htmlspecialchars($breadcrumb['text'], ENT_QUOTES, 'UTF-8'); ?></span>
        <?php } ?>
      <?php } ?>
    </nav>

    <section class="ecity-hero">
      <div class="ecity-hero__copy">
        <span class="ecity-kicker"><?php echo htmlspecialchars($city['region'], ENT_QUOTES, 'UTF-8'); ?></span>
        <h1><?php echo htmlspecialchars($city['name'], ENT_QUOTES, 'UTF-8'); ?> Prefabrik Ev ve Yapı Çözümleri</h1>
        <p><?php echo htmlspecialchars($city['lead'], ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="ecity-actions">
          <a class="ecity-btn ecity-btn--red" href="<?php echo htmlspecialchars($contact_url, ENT_QUOTES, 'UTF-8'); ?>">Projeniz İçin Teklif Al</a>
          <a class="ecity-btn ecity-btn--line" href="<?php echo htmlspecialchars($models_url, ENT_QUOTES, 'UTF-8'); ?>">Modelleri İncele</a>
        </div>
      </div>
      <aside class="ecity-hero__panel">
        <div>
          <small><?php echo htmlspecialchars($city['name'], ENT_QUOTES, 'UTF-8'); ?> İÇİN YEREL PLANLAMA</small>
          <h2><?php echo htmlspecialchars($city['headline'], ENT_QUOTES, 'UTF-8'); ?></h2>
          <p><?php echo htmlspecialchars($city['local_text'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="ecity-hero__facts"><span>Proje bazlı teknik kapsam</span><span>Saha ve güzergâh kontrolü</span><span>Üretim &amp; sevkiyat planı</span><span>Montaj organizasyonu</span></div>
      </aside>
    </section>
  </div>

  <section class="ecity-section ecity-section--soft">
    <div class="ecity-wrap">
      <div class="ecity-heading">
        <div><span class="ecity-label">BÖLGESEL DEĞERLENDİRME</span><h2><?php echo htmlspecialchars($city['name'], ENT_QUOTES, 'UTF-8'); ?> projesinde öne çıkan üç konu.</h2></div>
        <p>Doğru model seçimi yalnız metrekareye bağlı değildir. Yapının kullanılacağı yer ve sahadaki uygulama koşulları teknik kapsamı doğrudan etkiler.</p>
      </div>
      <div class="ecity-factor-grid">
        <?php foreach ($city['factors'] as $index => $factor) { ?>
        <article class="ecity-card"><span class="ecity-card__no">0<?php echo $index + 1; ?></span><h3><?php echo htmlspecialchars($factor['title'], ENT_QUOTES, 'UTF-8'); ?></h3><p><?php echo htmlspecialchars($factor['text'], ENT_QUOTES, 'UTF-8'); ?></p></article>
        <?php } ?>
      </div>
    </div>
  </section>

  <section class="ecity-section">
    <div class="ecity-wrap">
      <div class="ecity-split">
        <article class="ecity-dark">
          <span class="ecity-label">KULLANIM SENARYOLARI</span>
          <h2>İhtiyaca göre yapı seçenekleri</h2>
          <p>Hazır bir modeli her sahaya aynen uygulamak yerine kullanım amacı, kapasite ve teknik beklenti birlikte değerlendirilir.</p>
          <div class="ecity-use-list"><?php foreach ($city['uses'] as $use) { ?><span><?php echo htmlspecialchars($use, ENT_QUOTES, 'UTF-8'); ?></span><?php } ?></div>
        </article>
        <article class="ecity-light">
          <span class="ecity-label">HİZMET DEĞERLENDİRMESİ</span>
          <h2><?php echo htmlspecialchars($city['name'], ENT_QUOTES, 'UTF-8'); ?> ve öne çıkan ilçeler</h2>
          <p>Aşağıdaki ilçeler başta olmak üzere proje konumu, yol erişimi ve saha uygunluğu paylaşılır; teslim kapsamı teknik değerlendirme sonrasında netleştirilir.</p>
          <div class="ecity-districts"><?php foreach ($city['districts'] as $district) { ?><span><?php echo htmlspecialchars($district, ENT_QUOTES, 'UTF-8'); ?></span><?php } ?></div>
          <div class="ecity-note"><strong>Lojistik</strong><p><?php echo htmlspecialchars($city['logistics'], ENT_QUOTES, 'UTF-8'); ?></p></div>
        </article>
      </div>
    </div>
  </section>

  <section class="ecity-section ecity-section--soft">
    <div class="ecity-wrap">
      <div class="ecity-heading">
        <div><span class="ecity-label">PROJE AKIŞI</span><h2>Karardan montaja dört net aşama.</h2></div>
        <p>İlk görüşmede doğru bilgileri paylaşmak, teklif kapsamının ve uygulama programının daha sağlıklı hazırlanmasını sağlar.</p>
      </div>
      <div class="ecity-process">
        <article class="ecity-step"><b>01 / İHTİYAÇ</b><h3>Yapı ve kullanım</h3><p>m², oda planı, kullanım amacı ve bütçe öncelikleri belirlenir.</p></article>
        <article class="ecity-step"><b>02 / SAHA</b><h3>Konum ve erişim</h3><p>İlçe, parsel, yol, zemin ve çalışma alanı bilgileri değerlendirilir.</p></article>
        <article class="ecity-step"><b>03 / KAPSAM</b><h3>Teknik teklif</h3><p>Model, yalıtım, donanım, sevkiyat ve montaj kalemleri netleştirilir.</p></article>
        <article class="ecity-step"><b>04 / UYGULAMA</b><h3>Üretim ve montaj</h3><p>Onaylanan kapsam üretim ve saha programına alınır.</p></article>
      </div>
      <div class="ecity-note"><strong>Önemli</strong><p>İmar, ruhsat, zemin ve altyapı koşulları parsel ile belediyeye göre değişebilir. Uygulama öncesinde ilgili kurumdan güncel yasal koşulların doğrulanması gerekir.</p></div>
    </div>
  </section>

  <?php if (!empty($city_references_html)) { ?>
  <?php echo $city_references_html; ?>
  <?php } ?>

  <section class="ecity-section">
    <div class="ecity-wrap">
      <div class="ecity-heading">
        <div><span class="ecity-label">SIK SORULAN SORULAR</span><h2><?php echo htmlspecialchars($city['name'], ENT_QUOTES, 'UTF-8'); ?> prefabrik ev hakkında merak edilenler.</h2></div>
        <p>Projenize özel kesin bilgi için ilçe, yaklaşık m², kullanım amacı ve saha bilgilerini bizimle paylaşabilirsiniz.</p>
      </div>
      <div class="ecity-faq">
        <?php foreach ($city['faqs'] as $faq) { ?><details><summary><?php echo htmlspecialchars($faq['q'], ENT_QUOTES, 'UTF-8'); ?></summary><p><?php echo htmlspecialchars($faq['a'], ENT_QUOTES, 'UTF-8'); ?></p></details><?php } ?>
      </div>
      <div class="ecity-related" aria-label="Diğer hizmet bölgeleri">
        <?php foreach ($other_cities as $other) { ?><a href="<?php echo htmlspecialchars($other['href'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($other['name'], ENT_QUOTES, 'UTF-8'); ?> prefabrik ev</a><?php } ?>
      </div>
    </div>
  </section>

  <section class="ecity-wrap ecity-cta">
    <div><span class="ecity-label">EGESER PREFABRİK</span><h2><?php echo htmlspecialchars($city['name'], ENT_QUOTES, 'UTF-8'); ?> projenizi birlikte değerlendirelim.</h2><p>Yaklaşık m², yapı türü, ilçe ve saha bilginizi paylaşın; ekibimiz size uygun kapsam için iletişime geçsin.</p></div>
    <div class="ecity-actions"><a class="ecity-btn ecity-btn--red" href="<?php echo htmlspecialchars($contact_url, ENT_QUOTES, 'UTF-8'); ?>">Teklif Al</a><a class="ecity-btn ecity-btn--line" href="<?php echo htmlspecialchars($whatsapp_url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">WhatsApp</a></div>
  </section>

  <script type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
  <script type="application/ld+json"><?php echo json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
</main>
<?php echo $footer; ?>
