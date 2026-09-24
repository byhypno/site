<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row">
    <?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>">
      <?php echo $content_top; ?>
<?php
/* EGESER Projelerimiz V1
 * OpenCart 2.3.x / Egeser theme
 * Author: EGESER
 */
?>
<style id="egeser-projelerimiz-v1">
.egp1{--ink:#191919;--red:#ef2329;--line:#eadfd5;--soft:#fbf8f4;--muted:#6f6f6f;max-width:1240px;margin:0 auto;padding:8px 21px 70px;color:var(--ink)}
.egp1 *{box-sizing:border-box}
/* Projelerimiz breadcrumb: ürün/kategori sayfalarıyla aynı sade görünüm */
.container > .breadcrumb{
  background:transparent !important;
  border:0 !important;
  border-radius:0 !important;
  box-shadow:none !important;
  margin:14px 0 18px !important;
  padding:0 !important;
  min-height:auto !important;
}
.container > .breadcrumb > li{
  font-size:12px;
  color:#777;
}
.container > .breadcrumb > li + li:before{
  color:#b9b9b9 !important;
}
.container > .breadcrumb a{
  color:#777 !important;
  text-decoration:none !important;
}
.container > .breadcrumb a:hover{
  color:#ef2329 !important;
}

.egp1 a{text-decoration:none}
.egp1-eyebrow{font-size:12px;font-weight:900;letter-spacing:.12em;color:var(--red);text-transform:uppercase}
.egp1 h1,.egp1 h2,.egp1 h3{margin:0;letter-spacing:-.035em}
.egp1 p{color:var(--muted);line-height:1.7}
.egp1-hero{display:grid;grid-template-columns:1.1fr .9fr;border:1px solid var(--line);border-radius:28px;overflow:hidden;background:#fff;min-height:455px}
.egp1-hero-copy{padding:56px}
.egp1-hero h1{font-size:52px;line-height:1.02;margin:14px 0 20px;max-width:650px}
.egp1-hero-copy>p{font-size:16px;max-width:650px;margin:0}
.egp1-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:28px}
.egp1-btn{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 20px;border-radius:10px;font-weight:800;font-size:14px;border:1px solid var(--red)}
.egp1-btn--red{background:var(--red);color:#fff}
.egp1-btn--ghost{background:#fff;color:var(--red)}
.egp1-hero-panel{padding:34px;background:linear-gradient(145deg,#1a1a1a 0%,#1a1a1a 55%,#39201b 100%);color:#fff;display:flex;align-items:stretch}
.egp1-visual{width:100%;border:1px solid rgba(255,255,255,.12);border-radius:22px;padding:28px;display:flex;flex-direction:column;justify-content:space-between}
.egp1-visual small{color:#ffba42;font-weight:900;letter-spacing:.12em}
.egp1-visual h3{font-size:28px;line-height:1.1;margin:10px 0;color:#fff}
.egp1-visual p{color:#d1d1d1;margin:0}
.egp1-chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:28px}
.egp1-chip{font-size:12px;font-weight:800;padding:8px 11px;border-radius:999px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);color:#fff}
.egp1-trust{display:grid;grid-template-columns:repeat(4,1fr);border:1px solid var(--line);border-top:0;border-radius:0 0 20px 20px;overflow:hidden}
.egp1-trust>div{padding:20px;border-right:1px solid var(--line)}
.egp1-trust>div:last-child{border-right:0}
.egp1-trust strong{display:block;font-size:14px}
.egp1-trust span{display:block;margin-top:4px;font-size:12px;color:var(--muted)}
.egp1-section{padding:74px 0 0}
.egp1-head{display:grid;grid-template-columns:1fr 330px;gap:30px;align-items:end;margin-bottom:28px}
.egp1-head h2{font-size:40px;line-height:1.05;margin-top:9px}
.egp1-head p{margin:0}
.egp1-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.egp1-project{border:1px solid var(--line);border-radius:20px;padding:25px;background:#fff;min-height:220px;display:flex;flex-direction:column}
.egp1-project--dark{background:#1b1b1b;color:#fff;border-color:#1b1b1b}.egp1-project--dark h3,.egp1-project--dark .egp1-project-area{color:#fff}
.egp1-project--dark p{color:#cfcfcf}
.egp1-project-top{display:flex;align-items:center;justify-content:space-between;gap:15px}
.egp1-project-loc{font-size:12px;font-weight:900;color:var(--red)}
.egp1-project-area{font-size:13px;font-weight:900}
.egp1-project h3{font-size:25px;margin:17px 0 8px}
.egp1-project p{margin:0 0 18px}
.egp1-tags{display:flex;flex-wrap:wrap;gap:7px;margin-top:auto}
.egp1-tag{font-size:11px;padding:7px 9px;border-radius:999px;background:#f5f3f1;color:#555}
.egp1-project--dark .egp1-tag{background:#2b2b2b;color:#ddd}
.egp1-corp{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.egp1-corp-card{padding:24px;border:1px solid var(--line);border-radius:18px;background:var(--soft);min-height:190px}
.egp1-corp-card:nth-child(1){background:#1b1b1b;color:#fff;border-color:#1b1b1b}
.egp1-corp-card:nth-child(1) p{color:#cfcfcf}
.egp1-corp-card .num{font-size:11px;font-weight:900;color:#f0a000}
.egp1-corp-card h3{font-size:20px;margin:18px 0 8px}
.egp1-process{display:grid;grid-template-columns:repeat(6,1fr);gap:0;position:relative;margin-top:12px}
.egp1-process:before{content:"";position:absolute;top:21px;left:4%;right:4%;height:1px;background:#ddd}
.egp1-step{position:relative;padding:0 12px}
.egp1-step b{position:relative;z-index:2;display:flex;width:44px;height:44px;border:1px solid var(--red);border-radius:50%;align-items:center;justify-content:center;background:#fff;color:var(--red)}
.egp1-step h3{font-size:15px;margin:18px 0 7px;letter-spacing:0}
.egp1-step p{font-size:12px;line-height:1.55;margin:0}
.egp1-regions{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.egp1-region{display:block;padding:23px;border:1px solid var(--line);border-radius:18px;background:#fff;min-height:165px;transition:border-color .15s ease,box-shadow .15s ease}
.egp1-region:hover{border-color:var(--red);box-shadow:0 6px 18px rgba(239,35,41,.1)}
.egp1-region:first-child{background:linear-gradient(145deg,#1a1a1a,#352015);color:#fff;border-color:#1a1a1a}
.egp1-region:first-child p{color:#d5d5d5}
.egp1-region small{color:#e38b00;font-weight:900;letter-spacing:.1em}
.egp1-region h3{font-size:21px;margin:12px 0 7px}
.egp1-region p{font-size:13px;margin:0}
.egp1-faq{display:grid;gap:9px}
.egp1-faq details{overflow:hidden;border:1px solid var(--line);border-radius:15px;background:#fff;transition:.2s ease}
.egp1-faq details:hover{border-color:rgba(244,161,38,.60)}
.egp1-faq details[open]{background:linear-gradient(135deg,#fff,#fff6ea);border-color:rgba(244,161,38,.55);box-shadow:inset 3px 0 0 #F21B22}
.egp1-faq summary{list-style:none;cursor:pointer;padding:19px 52px 19px 20px;font-weight:800;position:relative}
.egp1-faq summary::-webkit-details-marker{display:none}
.egp1-faq summary:after{content:"+";position:absolute;right:20px;top:50%;transform:translateY(-50%);font-size:23px;color:#F4A126}
.egp1-faq details[open] summary:after{content:"−";color:#F21B22}
.egp1-answer{margin:0;padding:0 20px 20px;color:var(--muted);line-height:1.75}
.egp1-cta{margin-top:74px;padding:34px 38px;border-radius:22px;background:linear-gradient(115deg,#191919,#392019);color:#fff;display:flex;align-items:center;justify-content:space-between;gap:25px}
.egp1-cta h2{font-size:28px;margin-top:7px;color:#fff}
.egp1-cta p{color:#d1d1d1;margin:8px 0 0}
.egp1-hook{display:none}
@media(max-width:991px){
 .egp1-hero{grid-template-columns:1fr}.egp1-hero h1{font-size:44px}.egp1-hero-copy{padding:40px}.egp1-head{grid-template-columns:1fr}
 .egp1-corp{grid-template-columns:repeat(2,1fr)}.egp1-process{grid-template-columns:repeat(3,1fr);gap:30px 0}.egp1-process:before{display:none}
}
@media(max-width:767px){
 .egp1{padding-left:15px;padding-right:15px}.egp1-hero{border-radius:20px}.egp1-hero-copy{padding:28px 22px}.egp1-hero h1{font-size:36px}
 .egp1-hero-panel{padding:18px}.egp1-trust{grid-template-columns:repeat(2,1fr)}.egp1-trust>div:nth-child(2){border-right:0}
 .egp1-head h2{font-size:32px}.egp1-grid,.egp1-regions{grid-template-columns:1fr}.egp1-corp{grid-template-columns:1fr}
 .egp1-process{grid-template-columns:1fr}.egp1-step{display:grid;grid-template-columns:50px 1fr;column-gap:12px}.egp1-step h3{margin:3px 0}.egp1-step p{grid-column:2}
 .egp1-cta{align-items:flex-start;flex-direction:column;padding:28px 22px}.egp1-btn{width:100%}
 .egp1-faq details{border-radius:12px}.egp1-faq summary{padding:16px 44px 16px 16px;font-size:13px}.egp1-answer{padding:0 16px 16px;font-size:12px}
}
</style>

<div class="egp1">
  <section class="egp1-hero">
    <div class="egp1-hero-copy">
      <div class="egp1-eyebrow">TAMAMLANAN UYGULAMALAR</div>
      <h1>Prefabrik Yapı Projeleri ve Referanslar</h1>
      <p>Bireysel prefabrik evlerden kurumsal yapılara kadar farklı kullanım senaryolarına göre tamamlanan seçili proje örneklerini, proje yaklaşımımızla birlikte inceleyin.</p>
      <div class="egp1-actions">
        <a class="egp1-btn egp1-btn--red" href="/iletisim">Proje Teklifi Al</a>
        <a class="egp1-btn egp1-btn--ghost" href="/projelerimiz#egp1-bireysel">Referansları İncele</a>
      </div>
    </div>
    <div class="egp1-hero-panel">
      <div class="egp1-visual">
        <div>
          <small>PROJE YAKLAŞIMI</small>
          <h3>İhtiyaçtan üretime, sevkiyattan montaja.</h3>
          <p>Her uygulama; kullanım amacı, plan, teknik kapsam, saha erişimi ve kurulum koşulları birlikte değerlendirilerek planlanır.</p>
        </div>
        <div class="egp1-chips">
          <span class="egp1-chip">Bireysel</span>
          <span class="egp1-chip">Kurumsal</span>
          <span class="egp1-chip">Projelendirme</span>
          <span class="egp1-chip">Üretim</span>
          <span class="egp1-chip">Sevkiyat</span>
          <span class="egp1-chip">Montaj</span>
        </div>
      </div>
    </div>
  </section>

  <div class="egp1-trust">
    <div><strong>Bireysel Projeler</strong><span>Prefabrik yaşam yapıları</span></div>
    <div><strong>Kurumsal Projeler</strong><span>İşletme ve tesis yapıları</span></div>
    <div><strong>Projelendirme</strong><span>İhtiyaca göre planlama</span></div>
    <div><strong>Sevkiyat & Montaj</strong><span>Saha koşullarına göre organizasyon</span></div>
  </div>

  <div id="egp1-bireysel">
  <?php if (!empty($egeser_references_html)) { ?>
  <?php echo $egeser_references_html; ?>
  <?php } ?>
  </div>

  <section class="egp1-section">
    <div class="egp1-head">
      <div><div class="egp1-eyebrow">KURUMSAL REFERANSLAR</div><h2>Kurumsal prefabrik yapı uygulamaları.</h2></div>
      <p>Kurumsal yapılarda kapasite, fonksiyon, saha programı ve teknik beklentiler proje başlangıcında birlikte ele alınır.</p>
    </div>
    <div class="egp1-corp">
      <article class="egp1-corp-card"><span class="num">01</span><h3>Ofis & Yönetim</h3><p>İdari birimler, saha yönetimi ve proje ofisleri için planlanan prefabrik çözümler.</p></article>
      <article class="egp1-corp-card"><span class="num">02</span><h3>Yatakhane & Personel</h3><p>Personel kapasitesi, oda düzeni ve ortak alan ihtiyaçlarına göre ölçeklenebilir yapılar.</p></article>
      <article class="egp1-corp-card"><span class="num">03</span><h3>Yemekhane & Sosyal Alan</h3><p>Kapasite, servis akışı ve ortak kullanım ihtiyaçlarına göre tesis çözümleri.</p></article>
      <article class="egp1-corp-card"><span class="num">04</span><h3>Şantiye & Özel Proje</h3><p>Proje sahasına ve kullanım senaryosuna göre özelleştirilen kurumsal yapılar.</p></article>
    </div>
  </section>

  <section class="egp1-section">
    <div class="egp1-head">
      <div><div class="egp1-eyebrow">PROJE SÜRECİ</div><h2>İhtiyaçtan teslimata 6 net adım.</h2></div>
      <p>Teklif öncesi ihtiyaç analizinden üretim ve montaja kadar süreç tek akışta takip edilir.</p>
    </div>
    <div class="egp1-process">
      <div class="egp1-step"><b>01</b><h3>İhtiyaç Analizi</h3><p>Kullanım amacı, yaklaşık m² ve lokasyon.</p></div>
      <div class="egp1-step"><b>02</b><h3>Planlama</h3><p>Mimari yerleşim ve kullanım senaryosu.</p></div>
      <div class="egp1-step"><b>03</b><h3>Teknik Kapsam</h3><p>Yapı, yalıtım, doğrama ve tesisat.</p></div>
      <div class="egp1-step"><b>04</b><h3>Teklif & Onay</h3><p>Netleşen kapsam doğrultusunda teklif.</p></div>
      <div class="egp1-step"><b>05</b><h3>Üretim & Sevkiyat</h3><p>Kontrollü üretim ve saha sevki.</p></div>
      <div class="egp1-step"><b>06</b><h3>Montaj & Teslim</h3><p>Saha uygulaması ve teslim kontrolleri.</p></div>
    </div>
  </section>

  <section class="egp1-section">
    <div class="egp1-head">
      <div><div class="egp1-eyebrow">HİZMET BÖLGELERİ</div><h2>Ege Bölgesi için prefabrik yapı projeleri.</h2></div>
      <p>Kemalpaşa / İzmir merkezli üretim altyapısıyla proje lokasyonu; sevkiyat, montaj ve saha erişimi açısından değerlendirilir.</p>
    </div>
    <div class="egp1-regions">
      <a class="egp1-region" href="/izmir-prefabrik-ev"><small>MERKEZ BÖLGE</small><h3>İzmir Prefabrik Projeleri</h3><p>İzmir ve ilçelerinde bireysel ve kurumsal prefabrik yapı projeleri.</p></a>
      <a class="egp1-region" href="/manisa-prefabrik-ev"><small>EGE</small><h3>Manisa Prefabrik Projeleri</h3><p>Manisa ve ilçelerinde planlama, üretim, sevkiyat ve montaj organizasyonu.</p></a>
      <a class="egp1-region" href="/aydin-prefabrik-ev"><small>EGE</small><h3>Aydın Prefabrik Projeleri</h3><p>Proje kapsamına göre saha ve lojistik değerlendirmesi.</p></a>
      <a class="egp1-region" href="/balikesir-prefabrik-ev"><small>EGE / MARMARA</small><h3>Balıkesir Prefabrik Projeleri</h3><p>Lokasyon ve saha koşullarına göre proje organizasyonu.</p></a>
      <a class="egp1-region" href="/usak-prefabrik-ev"><small>EGE</small><h3>Uşak Prefabrik Projeleri</h3><p>Üretim ve sevkiyat planı saha ve erişim koşullarına göre hazırlanır.</p></a>
      <a class="egp1-region" href="/mugla-prefabrik-ev"><small>GÜNEY EGE</small><h3>Muğla Prefabrik Projeleri</h3><p>Proje lokasyonu, saha erişimi ve montaj koşulları birlikte değerlendirilir.</p></a>
    </div>
  </section>

  <section class="egp1-section" id="eg-proje-sss">
    <div class="egp1-head">
      <div><div class="egp1-eyebrow">SIK SORULAN SORULAR</div><h2>Prefabrik yapı projeleri hakkında merak edilenler.</h2></div>
      <p>Proje öncesinde en sık karşılaşılan temel sorular.</p>
    </div>
    <div class="egp1-faq">
      <details><summary>Projeler sadece İzmir ve Manisa’da mı uygulanıyor?</summary><div class="egp1-answer">Hayır. Proje lokasyonu; sevkiyat, saha erişimi, montaj ve uygulama koşulları açısından değerlendirilerek uygun bölgelerde çalışma planı oluşturulur.</div></details>
      <details><summary>Referanslardaki bir proje aynen uygulanabilir mi?</summary><div class="egp1-answer">Referans projeler fikir vermek amacıyla kullanılır. Kullanım amacı, arsa ve saha koşulları ile ihtiyaçlar değerlendirildikten sonra proje kapsamı netleştirilir.</div></details>
      <details><summary>Projeye başlamadan önce hangi bilgiler gerekir?</summary><div class="egp1-answer">Kullanım amacı, yaklaşık m², oda veya bölüm ihtiyacı ve proje lokasyonu ilk değerlendirme için yeterlidir.</div></details>
      <details><summary>Proje fotoğrafları sonradan eklenebilir mi?</summary><div class="egp1-answer">Evet. Referanslar modülü devreye alındığında proje kartları gerçek fotoğraf, lokasyon, m² ve proje detaylarıyla dinamik olarak genişletilebilir.</div></details>
    </div>
  </section>

  <div id="eg-proje-referanslar-hook" class="egp1-hook"></div>

  <section class="egp1-cta" id="egp1-teklif">
    <div><div class="egp1-eyebrow">PROJE DESTEĞİ</div><h2>Sizin projenizi de birlikte planlayalım.</h2><p>Kurulum yerinizi, yaklaşık m² bilginizi ve kullanım amacınızı paylaşın.</p></div>
    <a class="egp1-btn egp1-btn--red" href="/iletisim">Teklif Talebi Oluştur</a>
  </section>
</div>

      <?php echo $content_bottom; ?>
    </div>
    <?php echo $column_right; ?>
  </div>
</div>


<?php if (!empty($egeser_schema_url)) { ?>
<script type="application/ld+json"><?php
$eg_page_schema = array(
  '@context' => 'https://schema.org',
  '@type' => !empty($egeser_schema_type) ? $egeser_schema_type : 'WebPage',
  'name' => !empty($egeser_schema_name) ? strip_tags($egeser_schema_name) : strip_tags($heading_title),
  'url' => $egeser_schema_url
);
if (!empty($egeser_schema_description)) { $eg_page_schema['description'] = $egeser_schema_description; }
echo json_encode($eg_page_schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?></script>
<script type="application/ld+json"><?php
$eg_bc_items = array(); $eg_bc_pos = 1;
foreach ($breadcrumbs as $eg_bc) {
  $eg_bc_items[] = array('@type'=>'ListItem','position'=>$eg_bc_pos++,'name'=>trim(strip_tags($eg_bc['text'])),'item'=>html_entity_decode($eg_bc['href'], ENT_QUOTES, 'UTF-8'));
}
echo json_encode(array('@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>$eg_bc_items), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?></script>
<?php } ?>

<?php echo $footer; ?>
