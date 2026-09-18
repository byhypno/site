<?php echo $header; ?>
<?php
/* =========================================================
   EGESER UNIVERSAL CATEGORY TEMPLATE V1.2
   OpenCart 2.3.x
   Author: EGESER
   ========================================================= */

$eg_title = isset($heading_title) ? trim($heading_title) : '';

$eg_category_map = array(
    'Tek Katlı Prefabrik Evler' => array(
        'type' => 'residential_single',
        'segment' => 'BİREYSEL PREFABRİK EVLER',
        'hero_copy' => 'Tek seviyede konforlu yaşam için farklı m² ve plan seçeneklerine sahip tek katlı prefabrik ev modellerini inceleyin.',
        'hero_sub' => '1+1, 2+1 ve 3+1 kullanım senaryolarını; m², plan, teknik kapsam ve kurulum bölgesiyle birlikte değerlendirin.',
        'badge' => 'TEK KAT',
        'listing_title' => 'Tek Katlı Prefabrik Ev Modelleri',
        'region_keyword' => 'Tek Katlı Prefabrik Evler',
        'price_label' => 'TEK KATLI PREFABRİK EV FİYATLARI',
        'choice_title' => 'Yaşam ihtiyacınıza göre doğru tek katlı evi seçin.',
        'choice_copy' => 'Oda sayısını yalnızca bugün için değil; kullanım sıklığı, aile yapısı ve gelecekteki ihtiyaçlarla birlikte değerlendirin.',
        'lead_default' => 'Bireysel'
    ),
    'Çift Katlı Prefabrik Evler' => array(
        'type' => 'residential_double',
        'segment' => 'BİREYSEL PREFABRİK EVLER',
        'hero_copy' => 'Daha geniş yaşam alanı, kat ayrımı ve farklı plan seçenekleri sunan çift katlı prefabrik ev çözümlerini inceleyin.',
        'hero_sub' => 'Alt ve üst kat kullanımını; toplam m², oda dağılımı, merdiven kurgusu, teknik kapsam ve kurulum bölgesiyle birlikte değerlendirin.',
        'badge' => 'ÇİFT KAT',
        'listing_title' => 'Çift Katlı Prefabrik Ev Modelleri',
        'region_keyword' => 'Çift Katlı Prefabrik Evler',
        'price_label' => 'ÇİFT KATLI PREFABRİK EV FİYATLARI',
        'choice_title' => 'Kat kullanımına göre doğru çift katlı planı seçin.',
        'choice_copy' => 'Alt ve üst kat kullanımını aile yapısı, oda ihtiyacı, mahremiyet ve yaşam senaryosuyla birlikte değerlendirin.',
        'lead_default' => 'Bireysel'
    ),
    'Tüm Prefabrik Ev Modelleri' => array(
        'type' => 'residential_all',
        'segment' => 'BİREYSEL PREFABRİK EVLER',
        'hero_copy' => 'Tek katlı, çift katlı ve farklı oda planlarına sahip prefabrik ev modellerini tek sayfada karşılaştırın.',
        'hero_sub' => 'Kat sayısı, toplam m², oda planı, teknik kapsam ve kullanım senaryosunu birlikte değerlendirerek doğru modeli seçin.',
        'badge' => 'TÜM MODELLER',
        'listing_title' => 'Prefabrik Ev Modelleri',
        'region_keyword' => 'Prefabrik Evler',
        'price_label' => 'PREFABRİK EV FİYATLARI',
        'choice_title' => 'Yaşam ihtiyacınıza göre doğru prefabrik evi karşılaştırın.',
        'choice_copy' => 'Tek kat, çift kat, oda sayısı ve kullanım amacını birlikte değerlendirerek uygun modeli belirleyin.',
        'lead_default' => 'Bireysel'
    ),
    'Prefabrik Ofis ve Yönetim Binaları' => array(
        'type' => 'corporate_office',
        'segment' => 'KURUMSAL PREFABRİK YAPILAR',
        'hero_copy' => 'Ofis, idari alan ve yönetim birimleri için proje bazlı prefabrik yapı çözümlerini inceleyin.',
        'hero_sub' => 'Kullanıcı sayısı, oda dağılımı, toplantı alanları, teknik kapsam ve saha koşullarını birlikte değerlendirin.',
        'badge' => 'OFİS',
        'listing_title' => 'Prefabrik Ofis ve Yönetim Binası Modelleri',
        'region_keyword' => 'Prefabrik Ofis ve Yönetim Binaları',
        'price_label' => 'PREFABRİK OFİS FİYATLANDIRMASI',
        'choice_title' => 'Operasyonunuza uygun ofis planını birlikte belirleyin.',
        'choice_copy' => 'Personel sayısı, departman yapısı, toplantı alanı ve saha kullanımına göre doğru planı seçin.',
        'lead_default' => 'Kurumsal'
    ),
    'Prefabrik Yatakhane Binaları' => array(
        'type' => 'corporate_dormitory',
        'segment' => 'KURUMSAL PREFABRİK YAPILAR',
        'hero_copy' => 'Personel konaklaması için kapasite, oda düzeni ve ortak kullanım alanlarına göre prefabrik yatakhane çözümlerini inceleyin.',
        'hero_sub' => 'Kişi kapasitesi, oda dağılımı, ıslak hacimler, teknik kapsam ve saha koşullarını birlikte planlayın.',
        'badge' => 'YATAKHANE',
        'listing_title' => 'Prefabrik Yatakhane Çözümleri',
        'region_keyword' => 'Prefabrik Yatakhane Binaları',
        'price_label' => 'PREFABRİK YATAKHANE FİYATLANDIRMASI',
        'choice_title' => 'Kapasiteye göre doğru yatakhane planını belirleyin.',
        'choice_copy' => 'Kişi sayısı, oda düzeni, duş-WC ve ortak alan ihtiyacını birlikte değerlendirin.',
        'lead_default' => 'Kurumsal'
    ),
    'Prefabrik Yemekhane Binaları' => array(
        'type' => 'corporate_cafeteria',
        'segment' => 'KURUMSAL PREFABRİK YAPILAR',
        'hero_copy' => 'Personel kapasitesi, servis akışı ve mutfak kullanımına göre prefabrik yemekhane çözümlerini inceleyin.',
        'hero_sub' => 'Oturma kapasitesi, mutfak-servis alanı, tesisat ihtiyacı ve saha koşullarını proje bütününde değerlendirin.',
        'badge' => 'YEMEKHANE',
        'listing_title' => 'Prefabrik Yemekhane Çözümleri',
        'region_keyword' => 'Prefabrik Yemekhane Binaları',
        'price_label' => 'PREFABRİK YEMEKHANE FİYATLANDIRMASI',
        'choice_title' => 'Kapasiteye uygun yemekhane planını birlikte kurgulayın.',
        'choice_copy' => 'Kullanıcı sayısı, masa düzeni, servis akışı ve mutfak hacmini birlikte değerlendirin.',
        'lead_default' => 'Kurumsal'
    ),
    'Prefabrik Şantiye Yapıları' => array(
        'type' => 'corporate_site',
        'segment' => 'KURUMSAL PREFABRİK YAPILAR',
        'hero_copy' => 'Şantiye ofisi, yatakhane, yemekhane ve destek birimleri için modüler prefabrik çözümleri inceleyin.',
        'hero_sub' => 'Saha kapasitesi, kullanım süresi, birim dağılımı, sevkiyat ve montaj planını proje bazında netleştirin.',
        'badge' => 'ŞANTİYE',
        'listing_title' => 'Prefabrik Şantiye Yapıları',
        'region_keyword' => 'Prefabrik Şantiye Yapıları',
        'price_label' => 'PREFABRİK ŞANTİYE YAPISI FİYATLANDIRMASI',
        'choice_title' => 'Şantiyenize uygun yapı kombinasyonunu belirleyin.',
        'choice_copy' => 'Ofis, yatakhane, yemekhane, WC-duş ve destek birimlerini saha kapasitesine göre birlikte planlayın.',
        'lead_default' => 'Kurumsal'
    ),
    'Prefabrik Sosyal Tesis Yapıları' => array(
        'type' => 'corporate_social',
        'segment' => 'KURUMSAL PREFABRİK YAPILAR',
        'hero_copy' => 'Sosyal alan, dinlenme, eğitim ve ortak kullanım ihtiyaçları için prefabrik tesis çözümlerini inceleyin.',
        'hero_sub' => 'Kullanım amacı, kişi kapasitesi, iç plan, teknik kapsam ve saha koşullarını birlikte değerlendirin.',
        'badge' => 'SOSYAL TESİS',
        'listing_title' => 'Prefabrik Sosyal Tesis Çözümleri',
        'region_keyword' => 'Prefabrik Sosyal Tesis Yapıları',
        'price_label' => 'PREFABRİK SOSYAL TESİS FİYATLANDIRMASI',
        'choice_title' => 'Kullanım amacına uygun sosyal tesis planını belirleyin.',
        'choice_copy' => 'Kapasite, kullanım senaryosu ve ortak alan ihtiyacını birlikte planlayın.',
        'lead_default' => 'Kurumsal'
    ),
    'Özel Proje Prefabrik Yapılar' => array(
        'type' => 'corporate_custom',
        'segment' => 'ÖZEL PROJE PREFABRİK YAPILAR',
        'hero_copy' => 'Standart modellerin dışında kalan ölçü, plan ve kullanım ihtiyaçları için özel proje prefabrik çözümlerini inceleyin.',
        'hero_sub' => 'Kullanım senaryosu, toplam alan, birim sayısı, teknik şartlar ve saha koşullarını proje özelinde netleştirin.',
        'badge' => 'ÖZEL PROJE',
        'listing_title' => 'Özel Proje Prefabrik Yapılar',
        'region_keyword' => 'Özel Proje Prefabrik Yapılar',
        'price_label' => 'ÖZEL PROJE PREFABRİK FİYATLANDIRMASI',
        'choice_title' => 'İhtiyacınıza göre özel planı birlikte oluşturalım.',
        'choice_copy' => 'Standart planların dışındaki oda sayısı, kat kurgusu, kullanım amacı ve teknik talepleri birlikte değerlendirin.',
        'lead_default' => 'Kurumsal'
    )
);

$eg_universal = isset($eg_category_map[$eg_title]);
$eg_cfg = $eg_universal ? $eg_category_map[$eg_title] : array(
    'type' => 'generic',
    'segment' => 'PREFABRİK YAPILAR',
    'hero_copy' => 'İhtiyacınıza uygun prefabrik yapı çözümlerini, modelleri ve proje seçeneklerini inceleyin.',
    'hero_sub' => '',
    'badge' => 'PREFABRİK',
    'listing_title' => $eg_title,
    'region_keyword' => $eg_title,
    'price_label' => 'PROJE FİYATLANDIRMASI',
    'choice_title' => 'İhtiyacınıza uygun yapıyı birlikte belirleyin.',
    'choice_copy' => 'Kullanım amacı, alan ihtiyacı ve proje koşullarını birlikte değerlendirin.',
    'lead_default' => 'Bireysel'
);

$eg_type = $eg_cfg['type'];
$is_corporate = (strpos($eg_type, 'corporate_') === 0);
$is_residential = (strpos($eg_type, 'residential_') === 0);
$lead_default = $eg_cfg['lead_default'];

/* Plan / kullanım kartları */
if ($is_residential) {
    if ($eg_type === 'residential_double') {
        $eg_choices = array(
            array('tag'=>'3+1','title'=>'Dengeli Dubleks','copy'=>'Alt katta yaşam alanı, üst katta özel odalar için dengeli kullanım.','link'=>'#eg-products-title'),
            array('tag'=>'4+1','title'=>'Geniş Aile Yaşamı','copy'=>'Daha fazla oda ve ortak alan isteyen aileler için.','link'=>'#eg-products-title'),
            array('tag'=>'ÖZEL','title'=>'Özel Kat Kurgusu','copy'=>'Kat dağılımı ve oda yerleşimini projeye göre değerlendirin.','link'=>'#eg-category-offer')
        );
    } elseif ($eg_type === 'residential_all') {
        $eg_choices = array(
            array('tag'=>'TEK KAT','title'=>'Tek Katlı Prefabrik Evler','copy'=>'Merdivensiz, erişimi kolay ve tüm yaşam alanları tek seviyede.','link'=>'#eg-products-title'),
            array('tag'=>'ÇİFT KAT','title'=>'Çift Katlı Prefabrik Evler','copy'=>'Daha geniş yaşam alanı ve katlara ayrılmış kullanım.','link'=>'#eg-products-title'),
            array('tag'=>'ÖZEL','title'=>'İhtiyaca Göre Proje','copy'=>'Oda sayısı, kat kurgusu ve kullanım senaryosu özel olarak planlanabilir.','link'=>'#eg-category-offer')
        );
    } else {
        $eg_choices = array(
            array('tag'=>'1+1','title'=>'Kompakt Yaşam','copy'=>'Bağ evi, yazlık veya daha düşük m² ihtiyacı için.','link'=>'#eg-products-title'),
            array('tag'=>'2+1','title'=>'Aile Yaşamı','copy'=>'Salon, iki oda ve dengeli ortak yaşam alanı arayan aileler için.','link'=>'#eg-products-title'),
            array('tag'=>'3+1','title'=>'Geniş & Sürekli Kullanım','copy'=>'Daha fazla oda ve uzun süreli konfor beklentisi olan projeler için.','link'=>'#eg-category-offer')
        );
    }
} else {
    $eg_choices = array(
        array('tag'=>'01','title'=>'Kapasite','copy'=>'Kullanıcı/personel sayısına göre toplam alanı belirleyin.','link'=>'#eg-category-offer'),
        array('tag'=>'02','title'=>'Fonksiyon','copy'=>'Oda, bölüm ve ortak kullanım alanlarını ihtiyaca göre planlayın.','link'=>'#eg-category-offer'),
        array('tag'=>'03','title'=>'Saha','copy'=>'Sevkiyat, erişim, zemin ve montaj koşullarını birlikte değerlendirin.','link'=>'#eg-category-offer')
    );
}

/* SSS */
if ($is_residential) {
    $eg_faq_plan_q = $eg_title . ' planı değiştirilebilir mi?';
    $eg_faq_price_q = $eg_title . ' fiyatını hangi unsurlar etkiler?';

    if ($eg_type === 'residential_all') {
        $eg_faq_plan_q = 'Prefabrik ev planları değiştirilebilir mi?';
        $eg_faq_price_q = 'Prefabrik ev fiyatlarını hangi unsurlar etkiler?';
    }

    $egeser_faqs = array(
        array('question' => $eg_faq_plan_q, 'answer' => 'Mevcut planlar teknik uygunluk, taşıyıcı sistem ve üretim koşulları dikkate alınarak proje ihtiyacına göre değerlendirilebilir.'),
        array('question' => $eg_faq_price_q, 'answer' => 'Toplam m², oda sayısı, plan, teknik kapsam, malzeme tercihleri, sevkiyat mesafesi ve saha koşulları teklif çalışmasında birlikte değerlendirilir.'),
        array('question' => 'Prefabrik ev için temel veya zemin hazırlığı gerekir mi?', 'answer' => 'Uygun zemin ve temel hazırlığı montaj performansı açısından önemlidir. Uygulama şekli proje, arazi ve saha koşullarına göre netleştirilmelidir.'),
        array('question' => 'Prefabrik ev için ruhsat gerekir mi?', 'answer' => 'Ruhsat ve izin koşulları parselin niteliğine, belediye uygulamalarına ve projenin özelliklerine göre değişebilir. Uygulamadan önce ilgili idareden güncel şartların teyit edilmesi gerekir.'),
        array('question' => 'Üretim ve montaj süresi nasıl belirlenir?', 'answer' => 'Süre; yapı büyüklüğü, teknik kapsam, üretim yoğunluğu, saha hazırlığı ve montaj koşullarına göre değişir. Net program teklif ve proje onayı sonrasında belirlenir.'),
        array('question' => 'Sevkiyat ve montaj hizmeti veriliyor mu?', 'answer' => 'Sevkiyat ve montaj kapsamı proje lokasyonu, saha erişimi ve teknik şartlara göre teklif aşamasında planlanır.')
    );
} else {
    $egeser_faqs = array(
        array('question' => $eg_title . ' proje ölçülerine göre hazırlanabilir mi?', 'answer' => 'Proje ölçüleri, kullanım amacı, kapasite ve teknik şartlar değerlendirilerek uygun çözüm oluşturulabilir.'),
        array('question' => 'Fiyatlandırmayı hangi unsurlar etkiler?', 'answer' => 'Toplam alan, bölüm sayısı, teknik sistem, yalıtım, tesisat, sevkiyat ve montaj koşulları fiyatlandırmada birlikte değerlendirilir.'),
        array('question' => 'Sevkiyat ve montaj hizmeti veriliyor mu?', 'answer' => 'Sevkiyat ve montaj kapsamı proje lokasyonu, saha erişimi ve teknik şartlara göre teklif aşamasında planlanır.'),
        array('question' => 'Kurulum öncesinde zemin hazırlığı gerekir mi?', 'answer' => 'Zemin ve saha hazırlığı yapı tipi, proje ve uygulama koşullarına göre belirlenir.'),
        array('question' => 'Üretim süresi nasıl belirlenir?', 'answer' => 'Yapı büyüklüğü, teknik kapsam, adet ve üretim programına göre net süre proje onayı sonrasında belirlenir.'),
        array('question' => 'İzmir ve Manisa dışına uygulama yapılabilir mi?', 'answer' => 'Proje lokasyonu, sevkiyat ve montaj koşulları değerlendirilerek farklı bölgelere yönelik uygulamalar planlanabilir.')
    );
}
?>

<link rel="stylesheet" href="catalog/view/theme/egeser/stylesheet/egeser-category-universal.css">

<main id="content" class="eg-page eg-category-page eg-category-page--<?php echo htmlspecialchars($eg_type, ENT_QUOTES, 'UTF-8'); ?>">
  <div class="eg-container">

    <nav class="eg-breadcrumb" aria-label="Breadcrumb">
      <ol>
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ol>
    </nav>

    <header class="eg-category-hero eg-v18-hero">
      <div class="eg-category-hero__content">
        <span class="eg-section__eyebrow"><?php echo $eg_cfg['segment']; ?></span>
        <h1><?php echo $heading_title; ?></h1>
        <p><?php echo $eg_cfg['hero_copy']; ?></p>
        <?php if (!empty($eg_cfg['hero_sub'])) { ?><p class="eg-v18-hero__sub"><?php echo $eg_cfg['hero_sub']; ?></p><?php } ?>
        <div class="eg-category-hero__actions">
          <a class="eg-btn eg-btn--primary js-eg-category-offer-link" href="#eg-category-offer">Proje Teklifi Al</a>
          <a class="eg-btn eg-btn--outline" href="#eg-products-title">Modelleri İncele</a>
        </div>
        <div class="eg-category-hero__trust" aria-label="Hizmet avantajları">
          <span>✓ Projelendirme</span><span>✓ Üretim</span><span>✓ Sevkiyat</span><span>✓ Montaj</span>
        </div>
      </div>

      <div class="eg-category-hero__media eg-v18-hero__media">
        <?php
        $eg_hero_image = '';
        if (!empty($thumb)) {
            $eg_hero_image = $thumb;
        } elseif (!empty($products) && !empty($products[0]['thumb'])) {
            $eg_hero_image = $products[0]['thumb'];
        }
        ?>
        <?php if ($eg_hero_image) { ?>
          <img src="<?php echo $eg_hero_image; ?>" alt="<?php echo htmlspecialchars($heading_title, ENT_QUOTES, 'UTF-8'); ?>" width="760" height="560" loading="eager" decoding="async">
        <?php } else { ?>
          <div class="eg-v18-photo-placeholder eg-v18-photo-placeholder--clean" aria-label="<?php echo htmlspecialchars($heading_title, ENT_QUOTES, 'UTF-8'); ?> görsel alanı"></div>
        <?php } ?>
        <div class="eg-v18-hero__badge"><span><?php echo $eg_cfg['badge']; ?></span><strong>Plan • Üretim • Montaj</strong></div>
      </div>
    </header>

    <section class="eg-v108-summary eg-v18-summary" aria-label="Proje özeti">
      <div><em>01</em><strong>Planlama</strong><span>İhtiyaca göre kullanım kurgusu</span></div>
      <div><em>02</em><strong>Proje Desteği</strong><span>Teknik değerlendirme</span></div>
      <div><em>03</em><strong>Kontrollü Üretim</strong><span>Planlı üretim ve kalite akışı</span></div>
      <div><em>04</em><strong>Sevkiyat & Montaj</strong><span>Saha koşullarına göre organizasyon</span></div>
    </section>

    <?php echo $content_top; ?>

    <?php if ($categories) { ?>
    <section class="eg-subcategories" aria-labelledby="eg-subcat-title">
      <div class="eg-section__heading">
        <div><span class="eg-section__eyebrow">ALT KATEGORİLER</span><h2 id="eg-subcat-title">İhtiyacınıza Göre İnceleyin</h2></div>
      </div>
      <div class="eg-subcategory-grid">
        <?php foreach ($categories as $category) { ?>
          <a class="eg-subcategory-card" href="<?php echo $category['href']; ?>"><strong><?php echo $category['name']; ?></strong><span>İncele →</span></a>
        <?php } ?>
      </div>
    </section>
    <?php } ?>

    <section class="eg-v18-choice" aria-labelledby="eg-choice-title">
      <div class="eg-v18-section-head">
        <div><span class="eg-section__eyebrow"><?php echo $is_residential ? 'HANGİ PLAN SİZE UYGUN?' : 'HANGİ ÇÖZÜM SİZE UYGUN?'; ?></span><h2 id="eg-choice-title"><?php echo $eg_cfg['choice_title']; ?></h2></div>
        <p><?php echo $eg_cfg['choice_copy']; ?></p>
      </div>
      <div class="eg-v18-choice__grid">
        <?php foreach ($eg_choices as $choice) { ?>
        <article><span><?php echo $choice['tag']; ?></span><h3><?php echo $choice['title']; ?></h3><p><?php echo $choice['copy']; ?></p><a class="js-eg-category-offer-link" href="<?php echo $choice['link']; ?>">İncele / değerlendirelim →</a></article>
        <?php } ?>
      </div>
    </section>

    <?php if ($products) { ?>
    <section class="eg-listing eg-v18-listing" aria-labelledby="eg-products-title">
      <div class="eg-listing__head">
        <div>
          <span class="eg-section__eyebrow">MODELLER / ÇÖZÜMLER</span>
          <h2 id="eg-products-title"><?php echo $eg_cfg['listing_title']; ?></h2>
          <p>Detay sayfasında ürün bilgilerini, teknik özellikleri ve teklif seçeneklerini inceleyebilirsiniz.</p>
        </div>
        <div class="eg-listing__controls">
          <label>Sırala
            <select onchange="location=this.value">
              <?php foreach ($sorts as $sorts) { ?>
              <option value="<?php echo $sorts['href']; ?>"<?php if ($sorts['value'] == $sort . '-' . $order) { ?> selected<?php } ?>><?php echo $sorts['text']; ?></option>
              <?php } ?>
            </select>
          </label>
          <label>Göster
            <select onchange="location=this.value">
              <?php foreach ($limits as $limits) { ?>
              <option value="<?php echo $limits['href']; ?>"<?php if ($limits['value'] == $limit) { ?> selected<?php } ?>><?php echo $limits['text']; ?></option>
              <?php } ?>
            </select>
          </label>
        </div>
      </div>

      <div class="eg-products eg-products--listing<?php echo (count($products) === 1) ? ' eg-products--single' : ''; ?>">
        <?php foreach ($products as $product) { ?>
        <article class="eg-product-card eg-v18-product-card">
          <a class="eg-product-card__image" href="<?php echo $product['href']; ?>">
            <?php if ($product['thumb']) { ?><img src="<?php echo $product['thumb']; ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" decoding="async"><?php } ?>
          </a>
          <div class="eg-product-card__body">
            <div class="eg-v18-product-card__kicker"><?php echo $is_residential ? 'PREFABRİK EV MODELİ' : 'PREFABRİK YAPI ÇÖZÜMÜ'; ?></div>
            <h3><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h3>
            <?php if ($product['description']) { ?><p><?php echo $product['description']; ?></p><?php } ?>
            <div class="eg-v18-price-note"><span>PROJE BAZLI</span><strong>Fiyat / teklif için iletişime geçin</strong></div>
            <div class="eg-card-actions">
              <a class="eg-btn eg-btn--outline" href="<?php echo $product['href']; ?>">Detayları İncele</a>
              <a class="eg-btn eg-btn--primary js-eg-category-offer-link js-eg-model-offer" href="#eg-category-offer" data-product-id="<?php echo (int)$product['product_id']; ?>" data-product-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">Bu model için teklif al</a>
            </div>
          </div>
        </article>
        <?php } ?>
      </div>

      <div class="eg-listing__footer">
        <div><?php echo $results; ?></div><div><?php echo $pagination; ?></div>
      </div>
    </section>
    <?php } else { ?>
    <section class="eg-empty-category">
      <p>Bu kategoride henüz yayınlanmış ürün bulunmuyor. Proje ihtiyacınız için doğrudan teklif talebi oluşturabilirsiniz.</p>
      <a class="eg-btn eg-btn--primary js-eg-category-offer-link" href="#eg-category-offer">Teklif Talebi Oluştur</a>
    </section>
    <?php } ?>

    <?php if ($description) { ?>
    <section class="eg-category-detail-panel" aria-labelledby="eg-category-detail-title">
      <div class="eg-category-detail-panel__visual">
        <span class="eg-section__eyebrow">KATEGORİ DETAYI</span>
        <h2 id="eg-category-detail-title"><?php echo $heading_title; ?> Hakkında</h2>
        <p>Planlama, kullanım senaryosu ve teknik kapsamı tek bakışta değerlendirin.</p>
        <div class="eg-category-detail-panel__chips"><span>Planlama</span><span>Teknik Kapsam</span><span>Kurulum</span></div>
        <small>Kategori detayları proje koşullarına göre netleştirilir.</small>
      </div>
      <div class="eg-category-detail-panel__content">
        <span class="eg-section__eyebrow">KATEGORİ BİLGİLERİ</span>
        <h3><?php echo $heading_title; ?> size uygun mu?</h3>
        <p class="eg-category-detail-panel__intro">Kategori açıklaması, kullanım alanı ve öne çıkan detaylar aşağıda yer alır.</p>
        <div class="eg-richtext eg-category-detail-panel__richtext"><?php echo $description; ?></div>

        <div class="eg-category-detail-panel__points" aria-label="Kategori öne çıkan bilgiler">
          <?php if ($is_residential) { ?>
            <span>✓ Plan ve oda düzeni ihtiyaca göre değerlendirilebilir</span>
            <span>✓ Tek katlı ve/veya çift katlı alternatifler karşılaştırılabilir</span>
            <span>✓ Teknik kapsam proje şartlarına göre netleştirilir</span>
            <span>✓ Sevkiyat ve montaj koşulları lokasyona göre planlanır</span>
            <span>✓ Teklif, toplam proje kapsamına göre hazırlanır</span>
            <span>✓ Bireysel yaşam senaryosuna göre model seçimi yapılabilir</span>
          <?php } else { ?>
            <span>✓ Kapasite ve kullanım senaryosu birlikte değerlendirilir</span>
            <span>✓ Bölüm ve fonksiyon dağılımı projeye göre planlanabilir</span>
            <span>✓ Teknik kapsam ve tesisat ihtiyaçları netleştirilir</span>
            <span>✓ Sevkiyat ve montaj saha koşullarına göre planlanır</span>
            <span>✓ Proje bazlı teklif ve üretim planı hazırlanır</span>
            <span>✓ Kurumsal kullanım ihtiyaçlarına göre çözüm oluşturulur</span>
          <?php } ?>
        </div>
      </div>
    </section>
    <?php } ?>

    <section class="eg-v18-price-section" aria-labelledby="eg-price-title">
      <div class="eg-v18-price-section__intro">
        <span class="eg-section__eyebrow"><?php echo $eg_cfg['price_label']; ?></span>
        <h2 id="eg-price-title">Fiyatı yalnızca m² değil, projenin bütünü belirler.</h2>
        <p>Sağlıklı bir teklif için plan, teknik kapsam, kurulum bölgesi ve saha koşullarını birlikte değerlendiriyoruz.</p>
        <a class="eg-btn eg-btn--primary js-eg-category-offer-link" href="#eg-category-offer">Fiyat / Teklif Al</a>
      </div>
      <div class="eg-v18-price-factors">
        <div><span>01</span><strong>m² & Plan</strong><small>Toplam alan ve bölüm dağılımı</small></div>
        <div><span>02</span><strong>Kapasite / Oda</strong><small>Kullanım senaryosu ve plan</small></div>
        <div><span>03</span><strong>Duvar & Çatı</strong><small>Teknik sistem ve malzeme kapsamı</small></div>
        <div><span>04</span><strong>Yalıtım & Doğrama</strong><small>İklim ve kullanım senaryosu</small></div>
        <div><span>05</span><strong>İç Donanım</strong><small>Tesisat, mutfak, banyo ve özel uygulamalar</small></div>
        <div><span>06</span><strong>Sevkiyat & Montaj</strong><small>Lokasyon, saha erişimi ve kurulum koşulları</small></div>
      </div>
    </section>

    <section class="eg-v18-regions" aria-labelledby="eg-regions-title">
      <div class="eg-v18-section-head">
        <div><span class="eg-section__eyebrow">HİZMET BÖLGELERİ</span><h2 id="eg-regions-title">Ege Bölgesi için <?php
          $eg_region_heading_keyword = $eg_cfg['region_keyword'];
          if ($eg_type === 'residential_all') {
              $eg_region_heading_keyword = 'prefabrik ev';
          }
          echo mb_strtolower($eg_region_heading_keyword, 'UTF-8');
        ?> çözümleri.</h2></div>
        <p>Kemalpaşa / İzmir merkezli üretim altyapımızla proje lokasyonunu sevkiyat, montaj ve saha erişimi açısından değerlendiriyoruz.</p>
      </div>
      <div class="eg-v18-regions__grid">
        <?php
        $eg_regions = array(
          array('label'=>'MERKEZ BÖLGE','city'=>'İzmir','primary'=>true),
          array('label'=>'EGE','city'=>'Manisa','primary'=>false),
          array('label'=>'EGE','city'=>'Aydın','primary'=>false),
          array('label'=>'EGE','city'=>'Uşak','primary'=>false),
          array('label'=>'EGE / MARMARA','city'=>'Balıkesir','primary'=>false),
          array('label'=>'GÜNEY EGE','city'=>'Muğla','primary'=>false)
        );
        foreach ($eg_regions as $r) {
          $region_title = $r['city'] . ' ' . $eg_cfg['region_keyword'];
        ?>
        <article<?php echo $r['primary'] ? ' class="is-primary"' : ''; ?>>
          <span><?php echo $r['label']; ?></span>
          <h3><?php echo $region_title; ?></h3>
          <p><?php echo $r['city']; ?> ve çevresinde <?php echo mb_strtolower($eg_cfg['region_keyword'], 'UTF-8'); ?> projelerinde planlama, üretim, sevkiyat ve montaj koşulları proje bazında değerlendirilir.</p>
        </article>
        <?php } ?>
      </div>
    </section>

    <section class="eg-v18-process" aria-labelledby="eg-process-title">
      <div class="eg-v18-section-head">
        <div><span class="eg-section__eyebrow">PROJE SÜRECİ</span><h2 id="eg-process-title">İhtiyaçtan teslimata 6 net adım.</h2></div>
        <p>Teklif öncesi analizden üretim ve montaja kadar süreci tek akışta takip edin.</p>
      </div>
      <ol>
        <li><span>01</span><strong>İhtiyaç Analizi</strong><small>m², kapasite, kullanım amacı ve lokasyon.</small></li>
        <li><span>02</span><strong>Plan</strong><small>Model ve kullanım planının değerlendirilmesi.</small></li>
        <li><span>03</span><strong>Teknik Kapsam</strong><small>Yapı, yalıtım, doğrama ve tesisat detayları.</small></li>
        <li><span>04</span><strong>Teklif & Onay</strong><small>Proje kapsamına göre teklif ve üretim planı.</small></li>
        <li><span>05</span><strong>Üretim & Sevkiyat</strong><small>Kontrollü üretim ve saha sevki.</small></li>
        <li><span>06</span><strong>Montaj & Teslim</strong><small>Saha uygulaması ve teslim kontrolleri.</small></li>
      </ol>
    </section>

    <section class="eg-category-intent" aria-labelledby="eg-category-intent-title">
      <div>
        <span class="eg-section__eyebrow"><?php echo $is_corporate ? 'KURUMSAL PROJE' : 'PROJE DESTEĞİ'; ?></span>
        <h2 id="eg-category-intent-title"><?php echo $is_corporate ? 'Kurumsal Projenizi Birlikte Planlayalım' : 'İhtiyacınıza Uygun Yapıyı Birlikte Belirleyelim'; ?></h2>
        <p><?php echo $is_corporate ? 'Kullanım amacı, yaklaşık m² ve proje lokasyonunu iletin; teknik kapsam ve üretim sürecini birlikte netleştirelim.' : 'Kullanım amacı, yaklaşık m² ve kurulum bölgesini iletin; uygun model ve proje seçeneklerini birlikte değerlendirelim.'; ?></p>
      </div>
      <a class="eg-btn eg-btn--primary js-eg-category-offer-link" href="#eg-category-offer">Teklif Talebi Oluştur</a>
    </section>

    <section class="eg-category-faq" aria-labelledby="eg-category-faq-title">
      <span class="eg-section__eyebrow">SIK SORULAN SORULAR</span>
      <h2 id="eg-category-faq-title"><?php echo $heading_title; ?> Hakkında Merak Edilenler</h2>
      <div class="eg-faq-list">
        <?php foreach ($egeser_faqs as $faq) { ?>
        <details class="eg-faq-item"><summary><?php echo $faq['question']; ?></summary><div><?php echo $faq['answer']; ?></div></details>
        <?php } ?>
      </div>
    </section>

    <section class="eg-category-offer" id="eg-category-offer" aria-labelledby="eg-category-offer-title">
      <div class="eg-offer">
        <div class="eg-offer__content">
          <span class="eg-section__eyebrow">PROJE TEKLİFİ</span>
          <h2 id="eg-category-offer-title">Bu kategori için projenize özel teklif alın.</h2>
          <p>Kurulum yerinizi ve ihtiyacınızı paylaşın. Satış ekibimiz ürün ve proje kapsamına göre sizinle iletişime geçsin.</p>
          <div class="eg-offer__context-card" aria-label="Teklif alınan kategori">
            <div class="eg-offer__context-icon"><?php echo $eg_cfg['badge']; ?></div>
            <div><small id="eg-offer-context-label">Teklif alınan kategori</small><strong id="eg-offer-context-value"><?php echo htmlspecialchars($heading_title, ENT_QUOTES, 'UTF-8'); ?></strong></div>
          </div>
        </div>
        <?php
          $eg_form_context = 'category1';
          $eg_form_title = 'Teklif Bilgilerinizi Gönderin';
          $eg_form_product_id = 0;
          $eg_form_product_name = '';
          $eg_form_source = isset($heading_title) ? $heading_title : 'Kategori';
          $eg_form_default_customer_type = $lead_default;
          include(DIR_TEMPLATE . 'egeser/template/extension/module/egeser_lead_form.tpl');
        ?>
      </div>
    </section>

    <script>
    (function(){
      function ready(fn){
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', fn);
        } else {
          fn();
        }
      }

      ready(function(){
        var offerSection = document.getElementById('eg-category-offer');
        if (!offerSection) return;

        var form = offerSection.querySelector('form.js-egeser-lead-form');
        var productIdInput = form ? form.querySelector('input[name="product_id"]') : null;
        var productNameInput = form ? form.querySelector('input[name="product_name"]') : null;
        var contextLabel = document.getElementById('eg-offer-context-label');
        var contextValue = document.getElementById('eg-offer-context-value');
        var categoryName = <?php echo json_encode($heading_title, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

        document.addEventListener('click', function(e){
          var link = e.target.closest ? e.target.closest('.js-eg-category-offer-link') : null;
          if (!link) return;

          e.preventDefault();

          var productId = link.getAttribute('data-product-id') || '';
          var productName = link.getAttribute('data-product-name') || '';

          if (productIdInput) productIdInput.value = productId || '0';
          if (productNameInput) productNameInput.value = productName;

          if (contextLabel && contextValue) {
            if (productName) {
              contextLabel.textContent = 'Teklif alınan model';
              contextValue.textContent = productName;
            } else {
              contextLabel.textContent = 'Teklif alınan kategori';
              contextValue.textContent = categoryName;
            }
          }

          if (history && history.replaceState) {
            history.replaceState(null, '', window.location.pathname + window.location.search + '#eg-category-offer');
          }

          var top = offerSection.getBoundingClientRect().top + window.pageYOffset - 110;
          window.scrollTo({ top: top, behavior: 'smooth' });
        });
      });
    })();
    </script>

    <?php if (isset($content_bottom) && trim($content_bottom) !== '') { ?>
      <div class="eg-category-content-bottom"><?php echo $content_bottom; ?></div>
    <?php } ?>
  </div>
</main>

<script type="application/ld+json"><?php
$items = array(); $pos = 1;
foreach ($breadcrumbs as $b) {
  $items[] = array('@type'=>'ListItem','position'=>$pos++,'name'=>strip_tags($b['text']),'item'=>$b['href']);
}
echo json_encode(array('@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>$items), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?></script>

<?php if ($products) { ?>
<script type="application/ld+json"><?php
$list = array(); $position = 1;
foreach ($products as $p) {
  $list[] = array('@type'=>'ListItem','position'=>$position++,'item'=>$p['href'],'name'=>strip_tags($p['name']));
}
echo json_encode(array('@context'=>'https://schema.org','@type'=>'ItemList','name'=>strip_tags($heading_title),'itemListElement'=>$list), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?></script>
<?php } ?>

<script type="application/ld+json"><?php
$faq_entities = array();
foreach ($egeser_faqs as $faq) {
  $faq_entities[] = array('@type'=>'Question','name'=>strip_tags($faq['question']),'acceptedAnswer'=>array('@type'=>'Answer','text'=>strip_tags($faq['answer'])));
}
echo json_encode(array('@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>$faq_entities), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?></script>

<?php echo $footer; ?>
