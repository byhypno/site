<?php echo $header; ?>
<link rel="stylesheet" href="catalog/view/theme/egeser/stylesheet/egeser-hakkimizda-v2-1.css?v=20260918d">

<div class="ega">
  <div class="container">
    <ul class="breadcrumb ega-breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
      <?php } ?>
    </ul>

    <section class="ega-hero">
      <div class="ega-hero__content">
        <div class="ega-kicker">EGESER PREFABRİK</div>
        <h1>Hakkımızda</h1>
        <p class="ega-lead">
          Bireysel prefabrik evlerden kurumsal yapılara kadar; projelendirme, üretim,
          sevkiyat ve montaj süreçlerini tek merkezden yöneten bütüncül yapı çözümleri sunuyoruz.
        </p>
        <div class="ega-actions">
          <a href="#biz-kimiz" class="ega-btn ega-btn--primary">Bizi Tanıyın</a>
          <a href="#teklif" class="ega-btn ega-btn--ghost">Projenizi Konuşalım</a>
        </div>
        <div class="ega-trust">
          <span>✓ Projelendirme</span>
          <span>✓ Kontrollü Üretim</span>
          <span>✓ Sevkiyat</span>
          <span>✓ Montaj</span>
        </div>
      </div>

      <aside class="ega-hero__panel">
        <div class="ega-kicker ega-kicker--gold">YAPI YAKLAŞIMIMIZ</div>
        <h2>İhtiyaca göre planlanan, sahaya uygun çözümler.</h2>
        <p>
          Her projeyi kullanım amacı, yaklaşık m², saha koşulları, teknik kapsam
          ve uygulama bölgesiyle birlikte değerlendiriyoruz.
        </p>
        <div class="ega-chips">
          <span>Bireysel</span><span>Kurumsal</span><span>Tek Katlı</span>
          <span>Çift Katlı</span><span>Ofis</span><span>Şantiye</span>
        </div>
      </aside>
    </section>

    <section class="ega-section" id="biz-kimiz">
      <div class="ega-section__head">
        <div>
          <div class="ega-kicker">BİZ KİMİZ?</div>
          <h2>Egeser Prefabrik</h2>
        </div>
        <p>
          İzmir / Kemalpaşa merkezli çalışma yapımızla bireysel ve kurumsal prefabrik
          yapı projelerini tek çatı altında ele alıyoruz.
        </p>
      </div>

      <div class="ega-about">
        <div class="ega-about__text">
          <h3>Kuruluş Hikâyemiz</h3>
          <p>
            Egeser Prefabrik, <strong>2019</strong> yılında; kalite, güven
            ve uygulanabilirlik odağıyla prefabrik yapı çözümleri sunmak amacıyla kurulmuştur.
          </p>
          <p>
            Kurulduğu günden bu yana bireysel prefabrik evlerden ofis, yatakhane,
            yemekhane, şantiye, sosyal tesis ve özel proje yapılarına kadar farklı
            ihtiyaçlara yönelik çözümler geliştirmektedir.
          </p>
          <p>
            Hazır bir modeli herkese uygulamak yerine; kullanım amacı, plan,
            teknik kapsam, saha erişimi, sevkiyat ve montaj koşullarını proje
            başlangıcında birlikte değerlendirerek daha kontrollü bir süreç yürütmeyi hedefliyoruz.
          </p>
        </div>

        <div class="ega-about__facts">
          <div><b>Bireysel</b><span>Prefabrik yaşam alanları</span></div>
          <div><b>Kurumsal</b><span>Ofis, yatakhane, yemekhane, şantiye ve tesis yapıları</span></div>
          <div><b>Tek Merkez</b><span>Projelendirme, üretim, sevkiyat ve montaj</span></div>
        </div>
      </div>
    </section>

    <?php
    // EGESER: Yalnızca sunucuda gerçekten bulunan görselleri göster.
    // Böylece eksik showroom/üretim/ofis görselleri kırık <img> üretmez;
    // dosyalar daha sonra ilgili klasöre yüklendiğinde bölüm otomatik görünür.
    $ega_gallery_items = array(
      array(
        'file' => 'catalog/egeser/hakkimizda/showroom.jpg',
        'url' => 'image/catalog/egeser/hakkimizda/showroom.jpg',
        'alt' => 'Egeser Prefabrik Showroom',
        'class' => 'ega-gallery__item ega-gallery__item--large',
        'label' => 'SHOWROOM',
        'title' => 'Ürünleri yakından inceleyin',
        'text' => 'Prefabrik yapı çözümlerimizi showroom alanımızda daha yakından değerlendirebilirsiniz.'
      ),
      array(
        'file' => 'catalog/egeser/hakkimizda/uretim.jpg',
        'url' => 'image/catalog/egeser/hakkimizda/uretim.jpg',
        'alt' => 'Egeser Prefabrik Üretim Alanı',
        'class' => 'ega-gallery__item',
        'label' => 'ÜRETİM',
        'title' => 'Kontrollü üretim süreci',
        'text' => 'Projeye göre netleşen teknik kapsam kontrollü üretim akışıyla uygulanır.'
      ),
      array(
        'file' => 'catalog/egeser/hakkimizda/ofis.jpg',
        'url' => 'image/catalog/egeser/hakkimizda/ofis.jpg',
        'alt' => 'Egeser Prefabrik Ofis',
        'class' => 'ega-gallery__item',
        'label' => 'OFİS',
        'title' => 'Planlama ve proje koordinasyonu',
        'text' => 'Teklif, proje ve saha süreçleri ofis ekibimiz tarafından koordineli şekilde yürütülür.'
      )
    );

    $ega_gallery_available = array();
    foreach ($ega_gallery_items as $ega_gallery_item) {
      if (defined('DIR_IMAGE') && is_file(DIR_IMAGE . $ega_gallery_item['file'])) {
        $ega_gallery_available[] = $ega_gallery_item;
      }
    }
    ?>

    <?php if ($ega_gallery_available) { ?>
    <section class="ega-section">
      <div class="ega-section__head">
        <div>
          <div class="ega-kicker">EGESER'İ YAKINDAN TANIYIN</div>
          <h2>Showroom, üretim ve ofis yapımız.</h2>
        </div>
        <p>
          Müşteri deneyiminden üretim planlamasına ve proje koordinasyonuna kadar
          sürecin farklı aşamalarını kendi çalışma yapımız içinde yönetiyoruz.
        </p>
      </div>

      <div class="ega-gallery">
        <?php foreach ($ega_gallery_available as $ega_gallery_item) { ?>
        <figure class="<?php echo $ega_gallery_item['class']; ?>">
          <img src="<?php echo $ega_gallery_item['url']; ?>" alt="<?php echo htmlspecialchars($ega_gallery_item['alt'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
          <figcaption>
            <span><?php echo $ega_gallery_item['label']; ?></span>
            <h3><?php echo $ega_gallery_item['title']; ?></h3>
            <p><?php echo $ega_gallery_item['text']; ?></p>
          </figcaption>
        </figure>
        <?php } ?>
      </div>
    </section>
    <?php } ?>

    <section class="ega-section">
      <div class="ega-mv">
        <div class="ega-mv__card">
          <div class="ega-kicker">MİSYONUMUZ</div>
          <h2>Doğru ihtiyaca, doğru yapı çözümü.</h2>
          <p>
            Müşterilerimizin ihtiyaçlarına uygun, kaliteli, güvenilir ve uzun ömürlü
            prefabrik yapı çözümlerini; proje planlama, üretim, sevkiyat ve montaj
            süreçlerini uyumlu biçimde yöneterek sunmaktır.
          </p>
        </div>

        <div class="ega-mv__card ega-mv__card--dark">
          <div class="ega-kicker ega-kicker--gold">VİZYONUMUZ</div>
          <h2>Ege Bölgesi'nde güvenilir prefabrik yapı markası olmak.</h2>
          <p>
            İzmir ve Ege Bölgesi başta olmak üzere, bireysel ve kurumsal prefabrik
            yapı çözümlerinde kalite, güven ve sürdürülebilir hizmet anlayışıyla
            öne çıkan güçlü ve tercih edilen bir marka olmaktır.
          </p>
        </div>
      </div>
    </section>

    <section class="ega-section">
      <div class="ega-section__head">
        <div>
          <div class="ega-kicker">BİREYSEL + KURUMSAL</div>
          <h2>Farklı ihtiyaçlara tek üretim altyapısı.</h2>
        </div>
        <p>
          Yaşam alanlarından işletme ve tesis yapılarına kadar farklı kullanım
          senaryolarını aynı proje disiplini içinde ele alıyoruz.
        </p>
      </div>

      <div class="ega-dual">
        <div class="ega-solution">
          <div class="ega-kicker">BİREYSEL YAPILAR</div>
          <h3>Prefabrik Ev Çözümleri</h3>
          <p>
            Tek katlı ve çift katlı prefabrik evlerde; oda planı, kullanım alışkanlıkları,
            yapı büyüklüğü ve uygulama bölgesi birlikte değerlendirilir.
          </p>
        </div>

        <div class="ega-solution ega-solution--dark">
          <div class="ega-kicker ega-kicker--gold">KURUMSAL YAPILAR</div>
          <h3>Kurumsal Prefabrik Yapı Çözümleri</h3>
          <p>
            Ofis, yatakhane, yemekhane, şantiye, sosyal tesis ve özel projelerde
            kapasite, fonksiyon, saha koşulları ve teknik ihtiyaçlara göre çözüm oluşturulur.
          </p>
        </div>
      </div>
    </section>

    <section class="ega-section">
      <div class="ega-section__head">
        <div>
          <div class="ega-kicker">NEDEN EGESER?</div>
          <h2>Projeyi yalnızca ürün değil, süreç olarak ele alıyoruz.</h2>
        </div>
        <p>Doğru çözüm; yapı tipinin yanında doğru planlama ve uygulama disiplininden oluşur.</p>
      </div>

      <div class="ega-grid ega-grid--4">
        <div class="ega-card"><span>01</span><h3>Proje Bazlı Yaklaşım</h3><p>Kullanım amacı ve saha koşullarına göre değerlendirme.</p></div>
        <div class="ega-card"><span>02</span><h3>Tek Merkezden Süreç</h3><p>Planlama, üretim, sevkiyat ve montajın birbiriyle uyumu.</p></div>
        <div class="ega-card"><span>03</span><h3>Kontrollü Üretim</h3><p>Netleşen teknik kapsama göre planlı üretim akışı.</p></div>
        <div class="ega-card"><span>04</span><h3>Şeffaf İletişim</h3><p>Teknik kapsam ve uygulama detaylarının proje başında netleştirilmesi.</p></div>
      </div>
    </section>

    <section class="ega-section">
      <div class="ega-section__head">
        <div>
          <div class="ega-kicker">ÇALIŞMA SÜRECİMİZ</div>
          <h2>İhtiyaçtan teslimata 6 net adım.</h2>
        </div>
        <p>İlk görüşmeden saha uygulamasına kadar takip edilebilir bir süreç yürütüyoruz.</p>
      </div>

      <div class="ega-steps">
        <div><span>01</span><b>İhtiyaç Analizi</b><small>Kullanım amacı, yaklaşık m² ve lokasyon</small></div>
        <div><span>02</span><b>Projelendirme</b><small>Plan ve teknik kapsamın netleştirilmesi</small></div>
        <div><span>03</span><b>Teknik Kapsam</b><small>Yapı, yalıtım, doğrama ve tesisat</small></div>
        <div><span>04</span><b>Teklif & Onay</b><small>Netleşen kapsama göre teklif</small></div>
        <div><span>05</span><b>Üretim & Sevkiyat</b><small>Kontrollü üretim ve saha sevki</small></div>
        <div><span>06</span><b>Montaj & Teslim</b><small>Uygulama ve teslim kontrolleri</small></div>
      </div>
    </section>

    <section class="ega-section">
      <div class="ega-section__head">
        <div>
          <div class="ega-kicker">HİZMET BÖLGELERİ</div>
          <h2>Ege Bölgesi için prefabrik yapı çözümleri.</h2>
        </div>
        <p>
          Kemalpaşa / İzmir merkezli üretim altyapımızla proje lokasyonunu sevkiyat,
          montaj ve saha erişimi açısından değerlendiriyoruz.
        </p>
      </div>

      <div class="ega-grid ega-grid--3">
        <div class="ega-region ega-region--dark"><small>MERKEZ BÖLGE</small><h3>İzmir Prefabrik Ev ve Yapılar</h3><p>İzmir ve ilçelerinde bireysel ve kurumsal prefabrik yapı projeleri.</p></div>
        <div class="ega-region"><small>EGE</small><h3>Manisa Prefabrik Ev ve Yapılar</h3><p>Manisa ve çevresinde prefabrik ev ve kurumsal yapı çözümleri.</p></div>
        <div class="ega-region"><small>EGE</small><h3>Aydın Prefabrik Ev ve Yapılar</h3><p>Proje lokasyonu ve saha koşullarına göre planlama ve uygulama.</p></div>
        <div class="ega-region"><small>EGE</small><h3>Uşak Prefabrik Ev ve Yapılar</h3><p>Sevkiyat ve montaj koşullarına göre proje bazlı değerlendirme.</p></div>
        <div class="ega-region"><small>EGE / MARMARA</small><h3>Balıkesir Prefabrik Ev ve Yapılar</h3><p>Bireysel ve kurumsal kullanım için planlı prefabrik çözümler.</p></div>
        <div class="ega-region"><small>GÜNEY EGE</small><h3>Muğla Prefabrik Ev ve Yapılar</h3><p>Saha erişimi, sevkiyat ve montaj koşullarına göre uygulama.</p></div>
      </div>
    </section>

    <section class="ega-cta" id="teklif">
      <div>
        <div class="ega-kicker">PROJE DESTEĞİ</div>
        <h2>Projenizi birlikte planlayalım.</h2>
        <p>
          Prefabrik ev veya kurumsal prefabrik yapı ihtiyacınız için kullanım amacınızı,
          yaklaşık m² ihtiyacınızı ve uygulama bölgesini bizimle paylaşın.
        </p>
      </div>
      <a href="/iletisim" class="ega-btn ega-btn--primary">Teklif / Bilgi Al</a>
    </section>
  </div>
</div>

<?php echo $footer; ?>
