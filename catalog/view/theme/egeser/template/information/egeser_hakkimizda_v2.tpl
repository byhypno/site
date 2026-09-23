<?php echo $header; ?>
<link rel="stylesheet" href="catalog/view/theme/egeser/stylesheet/egeser-hakkimizda-v2-1.css">

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
          <a href="/projelerimiz" class="ega-btn ega-btn--primary">Bizi Tanıyın</a>
          <a href="/iletisim" class="ega-btn ega-btn--ghost">Projenizi Konuşalım</a>
        </div>
        <div class="ega-trust">
          <span>✓ Projelendirme</span>
          <span>✓ Kontrollü Üretim</span>
          <span>✓ Sevkiyat</span>
          <span>✓ Montaj</span>
        </div>
      </div>

      <aside class="ega-hero__panel">
        <?php if (defined('DIR_IMAGE') && is_file(DIR_IMAGE . 'catalog/egeser/hakkimizda/showroom.jpg')) { ?>
        <div class="ega-hero__panel-media">
          <img src="image/catalog/egeser/hakkimizda/showroom.jpg" alt="Egeser Prefabrik Showroom" loading="lazy">
        </div>
        <?php } ?>
        <div class="ega-hero__panel-body">
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
          <p>
            <strong>Egeser Prefabrik</strong>, lojistik sektöründe 20 yılı aşkın deneyime sahip
            <strong>Selçuk AÇAN</strong> ile prefabrik ve yapı sektöründe 25 yılı aşkın saha,
            üretim ve uygulama tecrübesine sahip <strong>Ferdi AKBULUT</strong>'un bilgi ve
            deneyimlerini bir araya getirmesiyle kurulmuştur.
          </p>
          <p>
            Farklı sektörlerde edinilen uzun yıllara dayanan tecrübenin ortak bir vizyonda
            buluşmasıyla ortaya çıkan Egeser Prefabrik; planlama, üretim, lojistik ve uygulama
            süreçlerini bütüncül bir anlayışla ele alan bir yapı markası olarak faaliyet göstermektedir.
          </p>

          <h3>Kuruluş Hikayemiz</h3>
          <p>
            Egeser Prefabrik'in temelleri, iki farklı uzmanlık alanının birbirini tamamlayan
            güçlü yönleri üzerine kurulmuştur.
          </p>
          <p>
            Lojistik sektöründe 20 yılı aşkın süre boyunca operasyon, planlama, sevkiyat ve
            organizasyon alanlarında deneyim kazanan <strong>Selçuk AÇAN</strong>; bu birikimini
            prefabrik yapı sektöründeki üretim süreçlerine taşımıştır.
          </p>
          <p>
            <strong>Ferdi AKBULUT</strong> ise prefabrik sektöründeki 25 yılı aşkın tecrübesiyle;
            üretimden malzeme seçimine, saha uygulamasından montaja kadar sektörün farklı
            aşamalarında edindiği bilgi birikimini Egeser Prefabrik çatısı altında bir araya getirmiştir.
          </p>
          <p>
            Bu ortaklık sayesinde lojistik disiplininin getirdiği doğru planlama ve zaman yönetimi;
            prefabrik sektörünün gerektirdiği teknik bilgi, üretim tecrübesi ve saha deneyimiyle
            birleşmiştir.
          </p>
          <p>
            Egeser Prefabrik'in bugün benimsediği çalışma anlayışının temelinde de bu birliktelik yer alır:
            <strong>doğru planlama, kaliteli üretim, kontrollü uygulama ve zamanında teslimat.</strong>
          </p>

          <h3>Ne Yapıyoruz?</h3>
          <p>
            Egeser Prefabrik olarak farklı kullanım ihtiyaçlarına yönelik
            <strong>prefabrik evler, çelik yapılar ve özel proje çözümleri</strong> üretiyoruz.
            Her projeyi yalnızca metrekare üzerinden değil; kullanım amacı, yerleşim planı,
            malzeme tercihleri ve uzun vadeli kullanım beklentileri doğrultusunda değerlendiriyoruz.
          </p>
          <p>
            Yaşam alanlarından ticari yapılara, sosyal alanlardan özel projelere kadar farklı
            ihtiyaçlara uygun yapı çözümleri geliştiriyor; projelendirme, üretim ve uygulama
            süreçlerini bir bütün olarak ele alıyoruz.
          </p>

          <h3>Üretim Anlayışımız</h3>
          <p>
            Prefabrik bir yapının kalitesinin yalnızca dış görünüşüyle ölçülemeyeceğine inanıyoruz.
            Bir yapının uzun yıllar güvenle kullanılabilmesi; taşıyıcı sisteminden duvar panellerine,
            çatı uygulamasından elektrik ve sıhhi tesisatına kadar tüm detayların doğru şekilde
            planlanmasına bağlıdır.
          </p>
          <p>
            Bu nedenle üretim süreçlerimizde malzeme seçimi, işçilik kalitesi ve uygulama detaylarını
            birlikte değerlendiriyoruz. Amacımız yalnızca kısa sürede yapı üretmek değil;
            <strong>kullanıcısına uzun vadede değer sağlayan yapılar</strong> ortaya çıkarmaktır.
          </p>

          <h3>Her Projeye Aynı Gözle Bakmıyoruz</h3>
          <p>
            Her müşterinin ihtiyacının ve her projenin koşullarının farklı olduğunun farkındayız.
            Bu nedenle standart bir ürünü herkese sunmak yerine, mümkün olan projelerde ihtiyaçlara
            göre planlama ve özelleştirme yapıyoruz.
          </p>
          <p>
            Oda yerleşimleri, cephe seçenekleri, veranda uygulamaları, panel tercihleri,
            iç mekan çözümleri ve farklı kullanım senaryoları proje özelliklerine göre değerlendirilebilir.
          </p>

          <h3>Neden Egeser Prefabrik?</h3>
          <ul>
            <li>Prefabrik sektöründe 25 yılı aşan teknik ve saha tecrübesi</li>
            <li>Lojistik ve operasyon alanında 20 yılı aşan deneyim</li>
            <li>Üretimden teslimata kadar planlı süreç yönetimi</li>
            <li>İhtiyaca göre geliştirilebilen proje çözümleri</li>
            <li>Malzeme ve uygulama detaylarına önem veren üretim anlayışı</li>
            <li>Satış öncesi ve satış sonrası iletişime önem veren yaklaşım</li>
            <li>Prefabrik ve çelik yapı alanında farklı kullanım amaçlarına yönelik çözümler</li>
          </ul>

          <h3>Üretimden Teslimata Aynı Sorumluluk</h3>
          <p>
            Bir yapının teslim edilmesi bizim için sürecin yalnızca son aşamasıdır.
            İlk görüşmeden proje planlamasına, üretimden sevkiyata ve saha uygulamasına kadar
            bütün aşamaların birbiriyle uyum içerisinde ilerlemesi gerektiğine inanıyoruz.
          </p>
          <p>
            Lojistik alanındaki tecrübemizin en önemli katkılarından biri de burada ortaya çıkar.
            Üretilen yapının doğru planlanması kadar, doğru zamanda ve doğru şekilde sahaya
            ulaştırılması da proje yönetiminin önemli bir parçasıdır.
          </p>

          <h3>Kalıcı Yapılar, Uzun Süreli Güven</h3>
          <p>
            Egeser Prefabrik olarak hedefimiz sadece daha fazla yapı üretmek değildir.
            Müşterilerimizin ihtiyaçlarını doğru anlayarak güvenilir, fonksiyonel ve uzun ömürlü
            yaşam alanları oluşturmayı amaçlıyoruz.
          </p>
          <p>
            Bugün sahip olduğumuz sektör tecrübesini modern üretim yöntemleriyle bir araya getirirken,
            sürekli gelişen yapı teknolojilerini ve müşteri beklentilerini de yakından takip ediyoruz.
          </p>
          <p>
            <strong>
              20+ yıllık lojistik tecrübesi ile 25+ yıllık prefabrik sektör deneyiminin
              birleşiminden doğan Egeser Prefabrik; tecrübeyi üretime, üretimi güvene dönüştürmeye
              devam ediyor.
            </strong>
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
        <a class="ega-region ega-region--dark" href="/izmir-prefabrik-ev"><small>MERKEZ BÖLGE</small><h3>İzmir Prefabrik Ev ve Yapılar</h3><p>İzmir ve ilçelerinde bireysel ve kurumsal prefabrik yapı projeleri.</p></a>
        <a class="ega-region" href="/manisa-prefabrik-ev"><small>EGE</small><h3>Manisa Prefabrik Ev ve Yapılar</h3><p>Manisa ve çevresinde prefabrik ev ve kurumsal yapı çözümleri.</p></a>
        <a class="ega-region" href="/aydin-prefabrik-ev"><small>EGE</small><h3>Aydın Prefabrik Ev ve Yapılar</h3><p>Proje lokasyonu ve saha koşullarına göre planlama ve uygulama.</p></a>
        <a class="ega-region" href="/usak-prefabrik-ev"><small>EGE</small><h3>Uşak Prefabrik Ev ve Yapılar</h3><p>Sevkiyat ve montaj koşullarına göre proje bazlı değerlendirme.</p></a>
        <a class="ega-region" href="/balikesir-prefabrik-ev"><small>EGE / MARMARA</small><h3>Balıkesir Prefabrik Ev ve Yapılar</h3><p>Bireysel ve kurumsal kullanım için planlı prefabrik çözümler.</p></a>
        <a class="ega-region" href="/mugla-prefabrik-ev"><small>GÜNEY EGE</small><h3>Muğla Prefabrik Ev ve Yapılar</h3><p>Saha erişimi, sevkiyat ve montaj koşullarına göre uygulama.</p></a>
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
