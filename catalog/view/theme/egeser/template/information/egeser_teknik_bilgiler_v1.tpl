<?php echo $header; ?>

<style id="egeser-technical-v1">
.et1{--red:#e72229;--ink:#191919;--muted:#6f6f6f;--line:#eadfd6;--soft:#fbf8f4;--amber:#f5a623;max-width:1240px;margin:0 auto;padding:0 21px 72px;color:var(--ink)}
.et1 *{box-sizing:border-box}
.et1 .breadcrumb{margin:14px 0 22px!important;padding:0!important;background:transparent!important;border:0!important;box-shadow:none!important}
.et1-eyebrow{display:block;font-size:12px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--red);margin-bottom:10px}
.et1-h1{font-size:52px;line-height:1.02;font-weight:500;letter-spacing:-.035em;margin:0 0 18px}
.et1-lead{font-size:18px;line-height:1.65;color:var(--muted);max-width:720px;margin:0}
.et1-hero{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(340px,.9fr);border:1px solid var(--line);border-radius:28px;overflow:hidden;background:#fff;margin:0 0 0}
.et1-hero__main{padding:54px 58px 52px}
.et1-hero__side{padding:30px;background:linear-gradient(135deg,#191919 0%,#191919 54%,#3a2019 100%);color:#fff;display:flex;align-items:stretch}
.et1-techbox{border:1px solid rgba(255,255,255,.14);border-radius:20px;padding:28px;width:100%;display:flex;flex-direction:column;justify-content:space-between}
.et1-techbox h2{font-size:30px;line-height:1.08;margin:8px 0 12px;color:#fff;font-weight:500}
.et1-techbox p{color:#d7d7d7;line-height:1.65;margin:0 0 20px}
.et1-pills{display:flex;gap:8px;flex-wrap:wrap}
.et1-pill{font-size:12px;font-weight:700;border:1px solid rgba(255,255,255,.18);border-radius:999px;padding:8px 11px;background:rgba(255,255,255,.06)}
.et1-strip{display:grid;grid-template-columns:repeat(4,1fr);border:1px solid var(--line);border-top:0;border-radius:0 0 22px 22px;overflow:hidden;margin-bottom:70px}
.et1-strip>div{padding:19px 20px;border-right:1px solid var(--line)}
.et1-strip>div:last-child{border-right:0}
.et1-strip strong{display:block;font-size:15px;margin-bottom:4px}
.et1-strip span{font-size:12px;color:var(--muted)}
.et1-section{margin:0 0 68px;scroll-margin-top:100px}
.et1-sectionhead{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:34px;align-items:end;margin-bottom:26px}
.et1-sectionhead h2{font-size:40px;line-height:1.08;font-weight:500;letter-spacing:-.025em;margin:0}
.et1-sectionhead p{margin:0;color:var(--muted);line-height:1.6}
.et1-navchips{display:flex;flex-wrap:wrap;gap:10px;padding:16px;border:1px solid var(--line);border-radius:18px;background:#fff}
.et1-navchips a{color:#3c3c3c;text-decoration:none;border:1px solid #e5dfd8;border-radius:999px;padding:10px 14px;font-size:13px;background:#fff}
.et1-grid3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.et1-card{border:1px solid var(--line);border-radius:20px;background:#fff;padding:26px}
.et1-card .num{display:inline-flex;min-width:38px;height:28px;align-items:center;justify-content:center;border-radius:999px;background:#fff1da;color:#cf7900;font-size:12px;font-weight:800;margin-bottom:18px}
.et1-card h3{font-size:22px;font-weight:500;margin:0 0 10px}
.et1-card p{margin:0;color:var(--muted);line-height:1.65}
.et1-two{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.et1-panel{border:1px solid var(--line);border-radius:22px;padding:30px;background:#fff}
.et1-panel h3{font-size:27px;font-weight:500;margin:0 0 12px}
.et1-panel p{color:var(--muted);line-height:1.7;margin:0 0 12px}
.et1-note{margin-top:18px;padding:15px 17px;border-left:4px solid var(--red);background:#fff6f6;border-radius:8px;color:#555}
.et1-dark{display:grid;grid-template-columns:.92fr 1.08fr;gap:28px;background:linear-gradient(120deg,#191919 0%,#191919 60%,#342018 100%);border-radius:24px;padding:38px;color:#fff}
.et1-dark h2{font-size:38px;line-height:1.08;margin:5px 0 14px;color:#fff;font-weight:500}
.et1-dark p{color:#d4d4d4;line-height:1.7;margin:0}
.et1-darkgrid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.et1-darkitem{border:1px solid rgba(255,255,255,.13);border-radius:16px;padding:18px;background:rgba(255,255,255,.035)}
.et1-darkitem strong{display:block;color:#fff;margin-bottom:5px}
.et1-darkitem span{font-size:12px;color:#c9c9c9}
.et1-grid4{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.et1-techcard{border:1px solid var(--line);border-radius:18px;padding:22px;background:#fff}
.et1-techcard h3{font-size:20px;font-weight:500;margin:0 0 9px}
.et1-techcard p{font-size:14px;line-height:1.6;color:var(--muted);margin:0}
.et1-ground{display:grid;grid-template-columns:1.18fr .82fr;gap:18px;background:#f6f6f5;border-radius:24px;padding:32px}
.et1-ground h2{font-size:34px;line-height:1.1;font-weight:500;margin:5px 0 14px}
.et1-ground p{color:var(--muted);line-height:1.7}
.et1-groundgrid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.et1-grounditem{background:#fff;border:1px solid #e4e4e1;border-radius:14px;padding:16px}
.et1-grounditem strong{display:block;margin-bottom:5px}
.et1-grounditem span{color:var(--muted);font-size:12px}
.et1-process{display:grid;grid-template-columns:repeat(6,1fr);gap:0;position:relative}
.et1-process:before{content:"";position:absolute;left:4%;right:4%;top:20px;height:1px;background:#e2ddd8}
.et1-step{position:relative;padding:0 10px}
.et1-step b{position:relative;z-index:1;display:flex;width:42px;height:42px;border-radius:50%;align-items:center;justify-content:center;background:#fff;border:1.5px solid var(--red);color:var(--red);font-size:12px;margin-bottom:18px}
.et1-step strong{display:block;margin-bottom:6px;font-size:14px}
.et1-step span{display:block;color:var(--muted);font-size:12px;line-height:1.5}
.et1-regions{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.et1-region{border:1px solid var(--line);border-radius:18px;padding:23px;background:#fff}
.et1-region:first-child{background:linear-gradient(135deg,#171717,#2e1b14);color:#fff;border-color:#1c1c1c}
.et1-region small{display:block;font-weight:800;letter-spacing:.1em;color:#ef9c18;text-transform:uppercase;margin-bottom:10px}
.et1-region h3{font-size:21px;font-weight:500;margin:0 0 8px}
.et1-region p{font-size:13px;line-height:1.6;color:var(--muted);margin:0}
.et1-region:first-child p{color:#ddd}
.et1-faq{display:grid;gap:9px}
.et1-faqitem{border:1px solid var(--line);border-radius:14px;overflow:hidden;background:#fff}
.et1-faqbtn{width:100%;border:0;background:#fff;text-align:left;padding:17px 18px;font-weight:700;display:flex;justify-content:space-between;align-items:center;cursor:pointer}
.et1-faqbtn:after{content:"+";font-size:22px;color:#df8b12}
.et1-faqitem.is-open .et1-faqbtn:after{content:"−";color:var(--red)}
.et1-faqanswer{display:none;padding:0 18px 18px;color:var(--muted);line-height:1.65}
.et1-faqitem.is-open .et1-faqanswer{display:block}
.et1-cta{display:grid;grid-template-columns:1fr auto;gap:28px;align-items:center;background:linear-gradient(110deg,#191919 0%,#191919 62%,#3a2019 100%);color:#fff;border-radius:24px;padding:34px 36px;margin-top:68px}
.et1-cta h2{font-size:31px;line-height:1.1;margin:5px 0 8px;color:#fff;font-weight:500}
.et1-cta p{margin:0;color:#d5d5d5}
.et1-btn{display:inline-flex;align-items:center;justify-content:center;background:var(--red);color:#fff!important;text-decoration:none!important;border-radius:10px;padding:14px 18px;font-weight:800;white-space:nowrap}
@media(max-width:900px){
 .et1-hero,.et1-dark,.et1-ground{grid-template-columns:1fr}
 .et1-sectionhead{grid-template-columns:1fr}
 .et1-strip{grid-template-columns:1fr 1fr}
 .et1-grid3,.et1-regions{grid-template-columns:1fr 1fr}
 .et1-grid4{grid-template-columns:1fr 1fr}
 .et1-process{grid-template-columns:1fr 1fr 1fr;gap:24px 0}
 .et1-process:before{display:none}
}
@media(max-width:600px){
 .et1{padding-left:14px;padding-right:14px}
 .et1-h1{font-size:37px}
 .et1-hero__main{padding:34px 26px}
 .et1-hero__side{padding:20px}
 .et1-strip,.et1-grid3,.et1-two,.et1-grid4,.et1-regions,.et1-groundgrid,.et1-process,.et1-cta{grid-template-columns:1fr}
 .et1-sectionhead h2{font-size:31px}
 .et1-dark h2{font-size:31px}
 .et1-cta{align-items:start}
}
</style>

<main id="content">
  <div class="container">
    <ul class="breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
      <?php } ?>
    </ul>
  </div>

  <div class="et1">
    <?php echo $content_top; ?>

    <section class="et1-hero">
      <div class="et1-hero__main">
        <span class="et1-eyebrow">Prefabrik Yapı Bilgi Merkezi</span>
        <h1 class="et1-h1">Prefabrik Yapı Teknik Bilgileri</h1>
        <p class="et1-lead">Duvar ve çatı sisteminden yalıtım, doğrama, elektrik ve sıhhi tesisata; temel hazırlığından montaj ve bakıma kadar prefabrik yapılarda teknik olarak bilinmesi gereken temel konular.</p>
      </div>
      <div class="et1-hero__side">
        <div class="et1-techbox">
          <div>
            <span class="et1-eyebrow" style="color:#f4b544">Teknik Kapsam</span>
            <h2>Doğru sistem, doğru saha, doğru uygulama.</h2>
            <p>Taşıyıcı sistem, duvar, çatı, yalıtım, tesisat ve saha koşulları tek tek değil; birbirini tamamlayan bir bütün olarak değerlendirilmelidir.</p>
          </div>
          <div class="et1-pills">
            <span class="et1-pill">Yapı Sistemi</span><span class="et1-pill">Yalıtım</span><span class="et1-pill">Doğrama</span>
            <span class="et1-pill">Tesisat</span><span class="et1-pill">Zemin</span><span class="et1-pill">Montaj</span>
          </div>
        </div>
      </div>
    </section>

    <div class="et1-strip">
      <div><strong>01 · Projelendirme</strong><span>Kullanım amacı ve teknik kapsam</span></div>
      <div><strong>02 · Kontrollü Üretim</strong><span>Sistem elemanlarının hazırlanması</span></div>
      <div><strong>03 · Saha Hazırlığı</strong><span>Zemin, erişim ve bağlantılar</span></div>
      <div><strong>04 · Montaj & Kontrol</strong><span>Planlı uygulama ve teslim</span></div>
    </div>

    <section class="et1-section">
      <div class="et1-sectionhead">
        <div>
          <span class="et1-eyebrow">Teknik Başlıklar</span>
          <h2>Prefabrik yapıyı oluşturan sistemi bütün olarak inceleyin.</h2>
        </div>
        <p>Tek bir bileşenin iyi olması yeterli değildir. Taşıyıcı sistemden saha uygulamasına kadar tüm parçaların proje koşullarına uyumlu olması gerekir.</p>
      </div>
      <div class="et1-navchips">
        <a href="#yapi-sistemi">Yapı Sistemi</a><a href="#duvar-cati">Duvar Sistemi</a><a href="#duvar-cati">Çatı Sistemi</a>
        <a href="#yalitim">Isı ve Ses Yalıtımı</a><a href="#tesisat">PVC Doğrama</a><a href="#tesisat">Elektrik</a>
        <a href="#tesisat">Sıhhi Tesisat</a><a href="#zemin">Temel / Zemin</a><a href="#montaj">Montaj</a><a href="#bakim">Bakım</a>
      </div>
    </section>

    <section class="et1-section" id="yapi-sistemi">
      <div class="et1-sectionhead">
        <div><span class="et1-eyebrow">Yapı Sistemi</span><h2>Prefabrik yapı sistemi nasıl çalışır?</h2></div>
        <p>Yapı elemanları kontrollü üretim sürecinde hazırlanır ve proje sahasında planlı biçimde bir araya getirilir.</p>
      </div>
      <div class="et1-grid3">
        <article class="et1-card"><span class="num">01</span><h3>Taşıyıcı Sistem</h3><p>Yapının yüklerini güvenli biçimde aktaran ana sistemdir. Kesitler ve bağlantı detayları proje boyutu, kullanım amacı ve statik ihtiyaçlara göre belirlenir.</p></article>
        <article class="et1-card"><span class="num">02</span><h3>Modüler Üretim Mantığı</h3><p>Üretim aşamalarının kontrollü ortamda yürütülmesi; saha montajında hız, tekrar edilebilirlik ve kalite kontrol avantajı sağlar.</p></article>
        <article class="et1-card"><span class="num">03</span><h3>Proje Uyumu</h3><p>Mimari plan, teknik sistemler, kullanım senaryosu ve saha koşulları birbirinden bağımsız değerlendirilmemelidir.</p></article>
      </div>
    </section>

    <section class="et1-section" id="duvar-cati">
      <div class="et1-two">
        <article class="et1-panel">
          <span class="et1-eyebrow">Duvar Sistemi</span>
          <h3>Duvar performansı yalnızca kalınlıktan ibaret değildir.</h3>
          <p>Dış ortam koşullarına karşı koruma, iç mekân konforu, ısı ve ses performansı ile yüzey dayanımı açısından duvar sistemi kritik bir bileşendir.</p>
          <p>Duvar kalınlığı, panel yapısı, iç ve dış yüzey malzemeleri; kullanım amacı, iklim ve proje gereksinimlerine göre belirlenmelidir.</p>
        </article>
        <article class="et1-panel">
          <span class="et1-eyebrow">Çatı Sistemi</span>
          <h3>Yağış, rüzgâr ve sıcaklık değişimlerine karşı doğru çatı çözümü.</h3>
          <p>Çatı formu, eğim, su tahliyesi, kaplama sistemi ve birleşim detayları proje bölgesinin iklim koşulları dikkate alınarak planlanmalıdır.</p>
          <div class="et1-note"><strong>Önemli:</strong> Çatı performansında yalnızca kaplama malzemesi değil; doğru eğim, birleşim detayları ve su tahliye çözümü de belirleyicidir.</div>
        </article>
      </div>
    </section>

    <section class="et1-section et1-dark" id="yalitim">
      <div>
        <span class="et1-eyebrow">Isı ve Ses Yalıtımı</span>
        <h2>Konforu tek bir malzeme değil, sistem bütünlüğü belirler.</h2>
        <p>Duvar, çatı, doğrama ve birleşim noktaları birlikte değerlendirilmelidir. Yalıtım seviyesi; yapının kullanılacağı bölge, sürekli veya dönemsel kullanım, iç mekân beklentileri ve enerji tüketimi hedeflerine göre belirlenir.</p>
      </div>
      <div class="et1-darkgrid">
        <div class="et1-darkitem"><strong>Duvar</strong><span>Katman yapısı ve birleşim detayları</span></div>
        <div class="et1-darkitem"><strong>Çatı</strong><span>Isı geçişi, su ve hava kontrolü</span></div>
        <div class="et1-darkitem"><strong>Doğrama</strong><span>Profil, cam kombinasyonu ve sızdırmazlık</span></div>
        <div class="et1-darkitem"><strong>Uygulama</strong><span>Detayların sahada doğru tamamlanması</span></div>
      </div>
    </section>

    <section class="et1-section" id="tesisat">
      <div class="et1-sectionhead">
        <div><span class="et1-eyebrow">Doğrama & Tesisat</span><h2>İç kullanım konforunu belirleyen teknik bileşenler.</h2></div>
        <p>Doğrama, elektrik ve sıhhi tesisat kararları yapının kullanım şekline ve saha bağlantılarına göre proje aşamasında netleştirilmelidir.</p>
      </div>
      <div class="et1-grid4">
        <article class="et1-techcard"><h3>PVC Doğrama</h3><p>Profil yapısı, açılım tipi ve montaj detayları; kullanım konforu, cephe ve havalandırma ihtiyaçlarına göre seçilmelidir.</p></article>
        <article class="et1-techcard"><h3>Cam Sistemi</h3><p>Cam kombinasyonu; ısı kaybı, gün ışığı, güvenlik ve kullanım senaryosu birlikte değerlendirilerek belirlenir.</p></article>
        <article class="et1-techcard"><h3>Elektrik Tesisatı</h3><p>Aydınlatma, priz grupları, sigorta panosu, kablo güzergâhları ve özel cihaz ihtiyaçları proje kapsamına göre planlanır.</p></article>
        <article class="et1-techcard"><h3>Sıhhi Tesisat</h3><p>Temiz su, atık su ve ıslak hacim bağlantıları; boru güzergâhları ve saha altyapısı dikkate alınarak çözülür.</p></article>
      </div>
    </section>

    <section class="et1-section et1-ground" id="zemin">
      <div>
        <span class="et1-eyebrow">Temel ve Zemin Hazırlığı</span>
        <h2>Sahadaki performans, yapı gelmeden önce başlar.</h2>
        <p>Yapı oturum alanı, kot, drenaj, taşıma kapasitesi, saha erişimi ve temel çözümü uygulama öncesinde değerlendirilmelidir.</p>
        <p>Hazırlığı tamamlanmamış veya uygun olmayan zemin; montaj, kapı-pencere ayarları, su tahliyesi ve uzun dönem kullanım üzerinde olumsuz etki oluşturabilir.</p>
      </div>
      <div class="et1-groundgrid">
        <div class="et1-grounditem"><strong>Zemin</strong><span>Kot, taşıma ve yüzey hazırlığı</span></div>
        <div class="et1-grounditem"><strong>Drenaj</strong><span>Yağmur ve yüzey suyunun yönetimi</span></div>
        <div class="et1-grounditem"><strong>Saha Erişimi</strong><span>Sevkiyat ve montaj ulaşımı</span></div>
        <div class="et1-grounditem"><strong>Bağlantılar</strong><span>Elektrik, temiz ve atık su altyapısı</span></div>
      </div>
    </section>

    <section class="et1-section" id="montaj">
      <div class="et1-sectionhead">
        <div><span class="et1-eyebrow">Montaj Süreci</span><h2>Üretimden teslimata, 6 net adım.</h2></div>
        <p>Montaj süresi; proje büyüklüğü, saha erişimi, hava koşulları ve teknik kapsama bağlı olarak proje bazında planlanır.</p>
      </div>
      <div class="et1-process">
        <div class="et1-step"><b>01</b><strong>Saha Kontrolü</strong><span>Ulaşım, montaj alanı ve zemin hazırlığı değerlendirilir.</span></div>
        <div class="et1-step"><b>02</b><strong>Sevkiyat</strong><span>Üretimi tamamlanan yapı elemanları planlanan programa göre sahaya ulaştırılır.</span></div>
        <div class="et1-step"><b>03</b><strong>Taşıyıcı Montajı</strong><span>Proje sırasına göre ana yapı sistemi oluşturulur.</span></div>
        <div class="et1-step"><b>04</b><strong>Tamamlayıcı İşler</strong><span>Çatı, doğrama ve dış kabuk bileşenleri tamamlanır.</span></div>
        <div class="et1-step"><b>05</b><strong>Tesisat Kontrolü</strong><span>Elektrik ve sıhhi tesisat işleri kontrol edilir.</span></div>
        <div class="et1-step"><b>06</b><strong>Teslim Kontrolü</strong><span>Tamamlanan yapı genel uygulama açısından gözden geçirilir.</span></div>
      </div>
    </section>

    <section class="et1-section" id="bakim">
      <div class="et1-sectionhead">
        <div><span class="et1-eyebrow">Bakım ve Kullanım</span><h2>Doğru kullanım, yapı performansının devamlılığını destekler.</h2></div>
        <p>Çatı ve yağmur suyu tahliye noktaları, dış yüzeyler, doğramalar, silikon ve birleşim detayları belirli aralıklarla kontrol edilmelidir.</p>
      </div>
      <div class="et1-note">Yapıda sonradan yapılacak delme, kesme, ağır ekipman sabitleme veya tesisat değişiklikleri; taşıyıcı sistem ve yalıtım detayları dikkate alınmadan uygulanmamalıdır.</div>
    </section>

    <section class="et1-section">
      <div class="et1-sectionhead">
        <div><span class="et1-eyebrow">Hizmet Bölgeleri</span><h2>Ege Bölgesi için prefabrik yapı teknik çözümleri.</h2></div>
        <p>Kemalpaşa / İzmir merkezli üretim altyapımızla proje lokasyonunu sevkiyat, montaj, saha erişimi ve teknik gereksinimler açısından değerlendiriyoruz.</p>
      </div>
      <div class="et1-regions">
        <article class="et1-region"><small>Merkez Bölge</small><h3>İzmir Prefabrik Yapı</h3><p>İzmir ve ilçelerinde prefabrik yapı projelerinde teknik kapsam, üretim, sevkiyat ve montaj koşulları proje bazında değerlendirilir.</p></article>
        <article class="et1-region"><small>Ege</small><h3>Manisa Prefabrik Yapı</h3><p>Manisa ve çevresinde saha koşulları, kullanım amacı ve teknik sistem gereksinimleri birlikte planlanır.</p></article>
        <article class="et1-region"><small>Ege</small><h3>Aydın Prefabrik Yapı</h3><p>Aydın projelerinde zemin, erişim, sevkiyat, montaj ve kullanım senaryosu teknik açıdan birlikte ele alınır.</p></article>
        <article class="et1-region"><small>Ege</small><h3>Uşak Prefabrik Yapı</h3><p>Uşak ve çevresinde prefabrik yapı teknik kapsamı saha erişimi ve kullanım koşullarına göre belirlenir.</p></article>
        <article class="et1-region"><small>Ege / Marmara</small><h3>Balıkesir Prefabrik Yapı</h3><p>Balıkesir projelerinde iklim, lokasyon, sevkiyat ve saha gereksinimleri proje bazında değerlendirilir.</p></article>
        <article class="et1-region"><small>Güney Ege</small><h3>Muğla Prefabrik Yapı</h3><p>Muğla ve çevresinde prefabrik yapı uygulamaları saha erişimi, zemin ve montaj şartlarına göre planlanır.</p></article>
      </div>
    </section>

    <section class="et1-section">
      <div class="et1-sectionhead">
        <div><span class="et1-eyebrow">Sık Sorulan Sorular</span><h2>Prefabrik yapılar hakkında teknik merak edilenler.</h2></div>
        <p>Teklif ve proje aşamasında en sık karşılaşılan teknik sorular.</p>
      </div>
      <div class="et1-faq">
        <div class="et1-faqitem"><button class="et1-faqbtn" type="button">Prefabrik yapıların duvar kalınlığı her projede aynı mıdır?</button><div class="et1-faqanswer">Hayır. Duvar sistemi; proje tipi, kullanım amacı, iklim koşulları ve teknik şartlara göre farklılaşabilir. Yalnızca kalınlık değil, katman yapısı ve uygulama detayları da değerlendirilmelidir.</div></div>
        <div class="et1-faqitem"><button class="et1-faqbtn" type="button">Prefabrik yapıda yalıtım yeterli olur mu?</button><div class="et1-faqanswer">Doğru duvar ve çatı sistemi, uygun doğrama, doğru birleşim detayları ve kaliteli uygulama birlikte ele alındığında iyi bir ısı ve ses performansı hedeflenebilir. Gerekli seviye proje bölgesine ve kullanım amacına göre belirlenir.</div></div>
        <div class="et1-faqitem"><button class="et1-faqbtn" type="button">Prefabrik yapı için beton zemin gerekli midir?</button><div class="et1-faqanswer">Temel ve zemin çözümü proje koşullarına göre belirlenir. Yapının oturacağı alanın düzgün, uygun kotta ve taşıma açısından yeterli olması önemlidir. Nihai çözüm saha ve proje değerlendirmesiyle netleştirilmelidir.</div></div>
        <div class="et1-faqitem"><button class="et1-faqbtn" type="button">Elektrik ve su tesisatı yapı içinde hazırlanabilir mi?</button><div class="et1-faqanswer">Proje kapsamına göre elektrik ve sıhhi tesisat altyapısı yapı içinde planlanabilir. Saha tarafındaki ana enerji, temiz su ve atık su bağlantıları ayrıca değerlendirilmelidir.</div></div>
        <div class="et1-faqitem"><button class="et1-faqbtn" type="button">Montaj süresi ne kadar sürer?</button><div class="et1-faqanswer">Süre; yapı büyüklüğü, proje tipi, saha erişimi, hava koşulları ve teknik kapsam gibi değişkenlere bağlıdır. Kesin süre proje planı üzerinden belirlenir.</div></div>
      </div>
    </section>

    <section class="et1-cta">
      <div>
        <span class="et1-eyebrow">Proje Desteği</span>
        <h2>Projenizin teknik kapsamını birlikte değerlendirelim.</h2>
        <p>Kullanım amacınızı, yaklaşık m² bilginizi ve uygulama lokasyonunu paylaşın; uygun yapı sistemi ve teknik kapsamı proje özelinde değerlendirelim.</p>
      </div>
      <a class="et1-btn" href="/iletisim">Teklif / Bilgi Al</a>
    </section>

    <?php echo $content_bottom; ?>
  </div>
</main>

<script>
(function(){
  var items=document.querySelectorAll('.et1-faqitem');
  for(var i=0;i<items.length;i++){
    (function(item){
      var b=item.querySelector('.et1-faqbtn');
      if(!b)return;
      b.addEventListener('click',function(){
        item.classList.toggle('is-open');
      });
    })(items[i]);
  }

  // Teknik Basliklar cip menusu: ayni sayfadaki #id hedeflerine
  // taraycidan bagimsiz olarak garantili kaydirma.
  var chips=document.querySelectorAll('.et1-navchips a[href^="#"]');
  for(var j=0;j<chips.length;j++){
    chips[j].addEventListener('click',function(e){
      var id=this.getAttribute('href').slice(1);
      var target=document.getElementById(id);
      if(!target)return;
      e.preventDefault();
      target.scrollIntoView({behavior:'smooth',block:'start'});
      if(history.pushState) history.pushState(null,'','#'+id);
    });
  }
})();
</script>

<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"FAQPage",
  "mainEntity":[
    {"@type":"Question","name":"Prefabrik yapıların duvar kalınlığı her projede aynı mıdır?","acceptedAnswer":{"@type":"Answer","text":"Hayır. Duvar sistemi proje tipi, kullanım amacı, iklim koşulları ve teknik şartlara göre farklılaşabilir. Yalnızca kalınlık değil, katman yapısı ve uygulama detayları da değerlendirilmelidir."}},
    {"@type":"Question","name":"Prefabrik yapıda yalıtım yeterli olur mu?","acceptedAnswer":{"@type":"Answer","text":"Doğru duvar ve çatı sistemi, uygun doğrama, doğru birleşim detayları ve kaliteli uygulama birlikte ele alındığında iyi bir ısı ve ses performansı hedeflenebilir."}},
    {"@type":"Question","name":"Prefabrik yapı için beton zemin gerekli midir?","acceptedAnswer":{"@type":"Answer","text":"Temel ve zemin çözümü proje koşullarına göre belirlenir. Nihai çözüm saha ve proje değerlendirmesiyle netleştirilmelidir."}},
    {"@type":"Question","name":"Elektrik ve su tesisatı yapı içinde hazırlanabilir mi?","acceptedAnswer":{"@type":"Answer","text":"Proje kapsamına göre elektrik ve sıhhi tesisat altyapısı yapı içinde planlanabilir. Saha tarafındaki ana bağlantılar ayrıca değerlendirilmelidir."}},
    {"@type":"Question","name":"Montaj süresi ne kadar sürer?","acceptedAnswer":{"@type":"Answer","text":"Süre yapı büyüklüğü, proje tipi, saha erişimi, hava koşulları ve teknik kapsama bağlıdır. Kesin süre proje planı üzerinden belirlenir."}}
  ]
}
</script>



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
