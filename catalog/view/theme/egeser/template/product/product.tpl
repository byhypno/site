<?php echo $header; ?>
<!-- EGESER Universal Product Template V1.1 -->
<?php
/*
 * Egeser V10.18 - Product Detail Polish Final
 * OpenCart 2.3.x / TPL compatible
 */
$eg_attr = array();
$eg_attr_props = array();
if (!empty($attribute_groups) && is_array($attribute_groups)) {
    foreach ($attribute_groups as $eg_group) {
        if (empty($eg_group['attribute']) || !is_array($eg_group['attribute'])) { continue; }
        foreach ($eg_group['attribute'] as $eg_a) {
            $eg_name = isset($eg_a['name']) ? trim(html_entity_decode(strip_tags($eg_a['name']), ENT_QUOTES, 'UTF-8')) : '';
            $eg_text = isset($eg_a['text']) ? trim(html_entity_decode(strip_tags($eg_a['text']), ENT_QUOTES, 'UTF-8')) : '';
            if ($eg_name !== '' && $eg_text !== '') {
                $eg_key = function_exists('mb_strtolower') ? mb_strtolower($eg_name, 'UTF-8') : strtolower($eg_name);
                $eg_attr[$eg_key] = $eg_text;
                $eg_attr_props[] = array('@type' => 'PropertyValue', 'name' => $eg_name, 'value' => $eg_text);
            }
        }
    }
}
$eg_find_attr = function($needles) use ($eg_attr) {
    foreach ($eg_attr as $k => $v) {
        foreach ((array)$needles as $needle) {
            $needle = function_exists('mb_strtolower') ? mb_strtolower($needle, 'UTF-8') : strtolower($needle);
            if (strpos($k, $needle) !== false) { return $v; }
        }
    }
    return '';
};
$eg_area = $eg_find_attr(array('m²','m2','metrekare','alan','toplam alan'));
if ($eg_area === '' && preg_match('/([0-9]{2,4})\s*(?:m²|m2|m\&sup2;)/iu', html_entity_decode(strip_tags($heading_title), ENT_QUOTES, 'UTF-8'), $eg_m)) {
    $eg_area = $eg_m[1] . ' m²';
}
$eg_rooms = $eg_find_attr(array('oda plan','oda say','oda','plan'));
if ($eg_rooms === '' && preg_match('/\b([1-9]\s*\+\s*[0-9])\b/u', strip_tags($heading_title), $eg_m)) { $eg_rooms = preg_replace('/\s+/', '', $eg_m[1]); }
$eg_floor = $eg_find_attr(array('kat say','kat'));
$eg_type = $eg_find_attr(array('yapı tipi','yapi tipi','ürün tipi','urun tipi'));
if ($eg_type === '') { $eg_type = 'Prefabrik Yapı'; }

/* EGESER UNIVERSAL PRODUCT TEMPLATE V1
   Ürün tipini ürün adı + breadcrumb + yapı tipi bilgisinden otomatik sınıflandırır. */
$eg_context_text = html_entity_decode(strip_tags($heading_title . ' ' . $eg_type), ENT_QUOTES, 'UTF-8');
if (!empty($breadcrumbs) && is_array($breadcrumbs)) {
    foreach ($breadcrumbs as $eg_bc_ctx) {
        if (!empty($eg_bc_ctx['text'])) { $eg_context_text .= ' ' . html_entity_decode(strip_tags($eg_bc_ctx['text']), ENT_QUOTES, 'UTF-8'); }
    }
}
$eg_context_lc = function_exists('mb_strtolower') ? mb_strtolower($eg_context_text, 'UTF-8') : strtolower($eg_context_text);

$eg_corporate_needles = array(
    'ofis','yönetim','yonetim','yatakhane','yemekhane','şantiye','santiye',
    'sosyal tesis','güvenlik','guvenlik','wc','duş','dus','kabin','kurumsal'
);
$eg_is_corporate = false;
foreach ($eg_corporate_needles as $eg_cn) {
    if (strpos($eg_context_lc, $eg_cn) !== false) { $eg_is_corporate = true; break; }
}

$eg_product_eyebrow = $eg_is_corporate ? 'KURUMSAL PREFABRİK YAPI' : 'PREFABRİK EV MODELİ';
$eg_pricing_eyebrow = $eg_is_corporate ? 'PROJE FİYATLANDIRMASI' : 'FİYATLANDIRMA';
$eg_pricing_title = $eg_is_corporate
    ? 'Projenize özel maliyeti birlikte netleştirelim.'
    : 'Bu evin size özel maliyetini birlikte netleştirelim.';
$eg_region_title = $eg_is_corporate
    ? 'İzmir ve Manisa için proje ve saha odaklı planlama.'
    : 'İzmir ve Manisa için saha odaklı planlama.';
$eg_form_default_customer_type = $eg_is_corporate ? 'Kurumsal' : 'Bireysel';
$eg_clean_desc = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($description), ENT_QUOTES, 'UTF-8')));
$eg_short_desc = 'Proje kapsamı, teknik özellikler, üretim, sevkiyat ve montaj seçenekleri ihtiyaca göre birlikte değerlendirilir.';
if ($eg_clean_desc !== '') {
    $eg_sentences = preg_split('/(?<=[.!?])\s+/u', $eg_clean_desc, -1, PREG_SPLIT_NO_EMPTY);
    if (!empty($eg_sentences)) {
        $eg_short_parts = array();
        $eg_short_len = 0;
        foreach ($eg_sentences as $eg_sentence) {
            $eg_sentence = trim($eg_sentence);
            if ($eg_sentence === '') { continue; }
            $eg_sentence_len = function_exists('mb_strlen') ? mb_strlen($eg_sentence, 'UTF-8') : strlen($eg_sentence);
            if (!empty($eg_short_parts) && ($eg_short_len + $eg_sentence_len) > 285) { break; }
            $eg_short_parts[] = $eg_sentence;
            $eg_short_len += $eg_sentence_len + 1;
            if (count($eg_short_parts) >= 2) { break; }
        }
        if (!empty($eg_short_parts)) { $eg_short_desc = implode(' ', $eg_short_parts); }
    }
}
$eg_current_url = !empty($share) ? $share : (!empty($egeser_current_url) ? $egeser_current_url : '');
$eg_image_schema = array();
if (!empty($popup)) { $eg_image_schema[] = $popup; }
if (!empty($images) && is_array($images)) { foreach ($images as $eg_i) { if (!empty($eg_i['popup'])) { $eg_image_schema[] = $eg_i['popup']; } } }
$eg_image_schema = array_values(array_unique($eg_image_schema));
$eg_offer_href = '#eg-lead';

/* V10.17: OpenCart 2.3 test data can expose untranslated zero-price tokens.
   Only show price / Offer schema when a real, non-zero price is present. */
$eg_price_rendered = isset($price) ? trim(html_entity_decode(strip_tags($price), ENT_QUOTES, 'UTF-8')) : '';
$eg_special_rendered = isset($special) ? trim(html_entity_decode(strip_tags($special), ENT_QUOTES, 'UTF-8')) : '';
$eg_price_probe = $eg_special_rendered !== '' ? $eg_special_rendered : $eg_price_rendered;
$eg_price_digits = preg_replace('/[^0-9]/', '', $eg_price_probe);
$eg_has_real_price = (
    $eg_price_probe !== '' &&
    strpos($eg_price_probe, 'decimal_point') === false &&
    strpos($eg_price_probe, 'text_') === false &&
    (int)$eg_price_digits > 0
);

/* =========================
   EGESER V11 UI DATA LAYER
   ========================= */
$eg_veranda = $eg_find_attr(array('veranda'));
$eg_pvc = $eg_find_attr(array('pvc doğrama','pvc dograma','doğrama','dograma'));
$eg_glass = $eg_find_attr(array('cam sistemi','ısıcam','isicam','cam'));
$eg_kitchen = $eg_find_attr(array('mutfak'));
$eg_bath = $eg_find_attr(array('banyo'));
$eg_flooring = $eg_find_attr(array('zemin'));
$eg_roof = $eg_find_attr(array('çatı sistemi','cati sistemi','çatı','cati'));
$eg_wall = $eg_find_attr(array('duvar kalınlığı','duvar kalinligi'));
$eg_exterior = $eg_find_attr(array('dış cephe','dis cephe'));
$eg_insulation = $eg_find_attr(array('ısı yalıtımı','isi yalitimi','yalıtım','yalitim'));

$eg_gallery_images = !empty($images) && is_array($images) ? $images : array();

/* EGESER Universal Product Template V1.1
   Kat planlarını tek / çift / çok katlı ürünlerde otomatik algılar. */
$eg_floor_plans = array();
$eg_plan_image = array();

if (!empty($eg_gallery_images)) {
    foreach ($eg_gallery_images as $eg_img_candidate) {
        $eg_probe = '';
        if (!empty($eg_img_candidate['popup'])) { $eg_probe .= ' ' . $eg_img_candidate['popup']; }
        if (!empty($eg_img_candidate['thumb'])) { $eg_probe .= ' ' . $eg_img_candidate['thumb']; }

        $eg_probe = function_exists('mb_strtolower')
            ? mb_strtolower($eg_probe, 'UTF-8')
            : strtolower($eg_probe);

        $eg_plan_label = '';

        if (
            strpos($eg_probe, 'zemin-kat-plani') !== false ||
            strpos($eg_probe, 'zemin_kat_plani') !== false ||
            strpos($eg_probe, 'zeminkatplani') !== false ||
            strpos($eg_probe, 'ground-floor') !== false ||
            strpos($eg_probe, 'ground_floor') !== false
        ) {
            $eg_plan_label = 'Zemin Kat Planı';
        } elseif (
            strpos($eg_probe, '1-kat-plani') !== false ||
            strpos($eg_probe, '1_kat_plani') !== false ||
            strpos($eg_probe, '1katplani') !== false ||
            strpos($eg_probe, 'birinci-kat-plani') !== false ||
            strpos($eg_probe, 'birinci_kat_plani') !== false ||
            strpos($eg_probe, 'first-floor') !== false ||
            strpos($eg_probe, 'first_floor') !== false ||
            strpos($eg_probe, 'ust-kat-plani') !== false ||
            strpos($eg_probe, 'üst-kat-plani') !== false
        ) {
            $eg_plan_label = '1. Kat Planı';
        } elseif (
            strpos($eg_probe, '2-kat-plani') !== false ||
            strpos($eg_probe, '2_kat_plani') !== false ||
            strpos($eg_probe, '2katplani') !== false ||
            strpos($eg_probe, 'ikinci-kat-plani') !== false ||
            strpos($eg_probe, 'ikinci_kat_plani') !== false ||
            strpos($eg_probe, 'second-floor') !== false ||
            strpos($eg_probe, 'second_floor') !== false
        ) {
            $eg_plan_label = '2. Kat Planı';
        } elseif (
            strpos($eg_probe, 'kat-plan') !== false ||
            strpos($eg_probe, 'kat_plani') !== false ||
            strpos($eg_probe, 'kat-plani') !== false ||
            strpos($eg_probe, 'katplani') !== false ||
            strpos($eg_probe, 'floor-plan') !== false ||
            strpos($eg_probe, 'floorplan') !== false ||
            strpos($eg_probe, '/plan') !== false
        ) {
            $eg_plan_label = 'Kat Planı';
        }

        if ($eg_plan_label !== '') {
            $eg_img_candidate['eg_plan_label'] = $eg_plan_label;
            $eg_floor_plans[] = $eg_img_candidate;
        }
    }
}

/* Eski değişkeni de koruyoruz; V1'in diğer alanları etkilenmesin. */
if (!empty($eg_floor_plans)) {
    $eg_plan_image = $eg_floor_plans[0];
} elseif (!empty($eg_gallery_images) && isset($eg_gallery_images[0])) {
    $eg_plan_image = $eg_gallery_images[0];
}

$eg_highlights = array();
$eg_highlight_candidates = array(
    array('label' => 'Veranda', 'value' => $eg_veranda),
    array('label' => 'PVC Doğrama', 'value' => $eg_pvc),
    array('label' => 'Cam Sistemi', 'value' => $eg_glass),
    array('label' => 'Mutfak', 'value' => $eg_kitchen),
    array('label' => 'Banyo', 'value' => $eg_bath),
    array('label' => 'Zemin', 'value' => $eg_flooring)
);
foreach ($eg_highlight_candidates as $eg_h) {
    if (trim($eg_h['value']) !== '') { $eg_highlights[] = $eg_h; }
}
if (count($eg_highlights) > 6) { $eg_highlights = array_slice($eg_highlights, 0, 6); }

$eg_spec_buckets = array(
    'Plan ve Model' => array(),
    'Yapı ve Yalıtım' => array(),
    'İç Mekân' => array(),
    'Elektrik ve Tesisat' => array(),
    'Diğer Teknik Bilgiler' => array()
);

$eg_bucket_for_name = function($name) {
    $n = function_exists('mb_strtolower') ? mb_strtolower($name, 'UTF-8') : strtolower($name);

    $plan_keys = array('alan','oda','kat','yapı tipi','yapi tipi','veranda');
    foreach ($plan_keys as $k) { if (strpos($n, $k) !== false) { return 'Plan ve Model'; } }

    $structure_keys = array('cephe','duvar','çatı','cati','yalıtım','yalitim');
    foreach ($structure_keys as $k) { if (strpos($n, $k) !== false) { return 'Yapı ve Yalıtım'; } }

    $interior_keys = array('tavan','zemin','pvc','cam','kapı','kapi','mutfak','banyo');
    foreach ($interior_keys as $k) { if (strpos($n, $k) !== false) { return 'İç Mekân'; } }

    $install_keys = array('elektrik','tesisat','sıhhi','sihhi');
    foreach ($install_keys as $k) { if (strpos($n, $k) !== false) { return 'Elektrik ve Tesisat'; } }

    return 'Diğer Teknik Bilgiler';
};

if (!empty($attribute_groups) && is_array($attribute_groups)) {
    foreach ($attribute_groups as $eg_group_v11) {
        if (empty($eg_group_v11['attribute']) || !is_array($eg_group_v11['attribute'])) { continue; }
        foreach ($eg_group_v11['attribute'] as $eg_attr_v11) {
            $eg_attr_name_v11 = isset($eg_attr_v11['name']) ? trim(strip_tags($eg_attr_v11['name'])) : '';
            $eg_attr_text_v11 = isset($eg_attr_v11['text']) ? trim(strip_tags($eg_attr_v11['text'])) : '';
            if ($eg_attr_name_v11 === '' || $eg_attr_text_v11 === '') { continue; }
            $eg_bucket = $eg_bucket_for_name($eg_attr_name_v11);
            $eg_spec_buckets[$eg_bucket][] = $eg_attr_v11;
        }
    }
}

$eg_spec_buckets = array_filter($eg_spec_buckets, function($items) { return !empty($items); });

// EGESER - Product CTA URL builder V1.0
$eg_phone_digits = !empty($telephone) ? preg_replace('/[^0-9+]/', '', $telephone) : '';
$eg_tel_href = $eg_phone_digits !== '' ? 'tel:' . $eg_phone_digits : (!empty($egeser_url_iletisim) ? $egeser_url_iletisim : '/iletisim');

$eg_wa_phone = !empty($egeser_whatsapp) ? preg_replace('/\D+/', '', $egeser_whatsapp) : '';
$eg_wa_message = 'Merhaba, ' . trim(strip_tags($heading_title)) . ' modeli hakkında bilgi ve teklif almak istiyorum.';
if (!empty($eg_current_url)) {
    $eg_wa_message .= ' Sayfa: ' . $eg_current_url;
}
$eg_wa_href = $eg_wa_phone !== ''
    ? 'https://wa.me/' . $eg_wa_phone . '?text=' . rawurlencode($eg_wa_message)
    : (!empty($egeser_url_iletisim) ? $egeser_url_iletisim : '/iletisim');

?>

<style id="egeser-product-v11-1">
:root{
  --eg11-red:#d71920;
  --eg11-red-dark:#b81017;
  --eg11-ink:#171717;
  --eg11-text:#2c2c2c;
  --eg11-muted:#6f6f72;
  --eg11-soft:#f6f6f4;
  --eg11-soft-2:#f0f0ed;
  --eg11-line:#e5e5e1;
  --eg11-white:#fff;
  --eg11-green:#0f8f7f;
  --eg11-radius-xl:30px;
  --eg11-radius-lg:22px;
  --eg11-radius-md:16px;
  --eg11-shadow:0 24px 70px rgba(20,20,20,.08);
}
.eg-v11{font-family:Arial,Helvetica,sans-serif;color:var(--eg11-text);background:#fff;font-size:15px;line-height:1.65;padding-bottom:90px}
.eg-v11 *{box-sizing:border-box}
.eg-v11 a{text-decoration:none}
.eg-v11 .eg11-container{width:min(calc(100% - 42px),1240px);margin:0 auto}
.eg-v11 .eg11-breadcrumb{padding:22px 0 8px}
.eg-v11 .eg11-breadcrumb ol{display:flex;flex-wrap:wrap;gap:7px;margin:0;padding:0;list-style:none;font-size:12px}
.eg-v11 .eg11-breadcrumb li:not(:last-child)::after{content:"/";margin-left:7px;color:#bbb}
.eg-v11 .eg11-breadcrumb a{color:#777}

/* HERO */
.eg-v11 .eg11-hero{display:grid;grid-template-columns:minmax(0,1.18fr) minmax(400px,.82fr);gap:54px;align-items:start;padding:28px 0 52px}
.eg-v11 .eg11-gallery{min-width:0}
.eg-v11 .eg11-main-media{position:relative;display:grid;place-items:center;width:100%;aspect-ratio:4/3;overflow:hidden;border-radius:var(--eg11-radius-xl);background:#f2f2ef;box-shadow:var(--eg11-shadow);isolation:isolate}
.eg-v11 .eg11-main-media img{display:block;width:100%;height:100%;aspect-ratio:auto;object-fit:contain;object-position:center;transition:opacity .2s ease}
.eg-v11 .eg11-main-media__placeholder{display:grid;place-items:center;min-height:570px;background:
 radial-gradient(circle at 20% 10%,rgba(215,25,32,.06),transparent 34%),
 linear-gradient(145deg,#f8f8f6,#efefeb);color:#888;text-align:center}
.eg-v11 .eg11-main-media__placeholder svg{width:68px;height:68px;margin-bottom:16px;color:#c4c4c0}
.eg-v11 .eg11-media-badge{position:absolute;left:20px;top:20px;z-index:2;padding:9px 12px;border-radius:999px;background:rgba(255,255,255,.92);backdrop-filter:blur(8px);font-size:11px;font-weight:800;letter-spacing:.06em;color:#333}
.eg-v11 .eg11-media-zoom{position:absolute;right:18px;bottom:18px;width:44px;height:44px;display:grid;place-items:center;border:0;border-radius:50%;background:rgba(20,20,20,.82);color:#fff;cursor:pointer}
.eg-v11 .eg11-thumbs{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:10px;margin-top:12px}
.eg-v11 .eg11-thumb{position:relative;display:block;width:100%;aspect-ratio:4/3;overflow:hidden;border:2px solid transparent;border-radius:13px;background:#f3f3f1;padding:0;cursor:pointer}
.eg-v11 .eg11-thumb img{display:block;width:100%;height:100%;object-fit:cover;object-position:center}
.eg-v11 .eg11-thumb.is-active{border-color:var(--eg11-red)}

.eg-v11 .eg11-summary{position:sticky;top:88px;padding:12px 0 0}
.eg-v11 .eg11-kicker{display:inline-flex;align-items:center;gap:8px;margin-bottom:14px;font-size:11px;font-weight:900;letter-spacing:.14em;color:var(--eg11-red);text-transform:uppercase}
.eg-v11 .eg11-kicker:before{content:"";width:28px;height:2px;background:currentColor}
.eg-v11 h1{margin:0 0 17px;color:var(--eg11-ink);font-size:clamp(42px,4.3vw,64px);line-height:.98;letter-spacing:-.045em;font-weight:900}
.eg-v11 .eg11-summary__intro{margin:0 0 24px;color:#5f6062;font-size:16px;line-height:1.8;max-width:610px}
.eg-v11 .eg11-keyfacts{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px;margin:0 0 18px}
.eg-v11 .eg11-keyfact{min-width:0;padding:14px 13px;border:1px solid var(--eg11-line);border-radius:14px;background:#fff}
.eg-v11 .eg11-keyfact svg{width:20px;height:20px;color:var(--eg11-red);margin-bottom:9px}
.eg-v11 .eg11-keyfact small{display:block;color:#8a8a8d;font-size:9px;letter-spacing:.08em;font-weight:800;text-transform:uppercase}
.eg-v11 .eg11-keyfact strong{display:block;margin-top:2px;color:#222;font-size:14px;line-height:1.25}
.eg-v11 .eg11-quote-card{padding:20px;border-radius:18px;background:#1e1e1e;color:#fff}
.eg-v11 .eg11-quote-card__top{display:flex;justify-content:space-between;gap:14px;align-items:flex-start}
.eg-v11 .eg11-quote-card__top strong{font-size:19px;line-height:1.25}
.eg-v11 .eg11-quote-card__top span{display:block;margin-top:4px;color:#c9c9c9;font-size:12px;line-height:1.5}
.eg-v11 .eg11-price{font-size:27px;font-weight:900;color:#fff}
.eg-v11 .eg11-actions{display:grid;grid-template-columns:1.2fr 1fr 1fr;gap:9px;margin-top:16px}
.eg-v11 .eg11-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:48px;padding:11px 14px;border-radius:11px;border:1px solid transparent;font-size:13px;font-weight:900;transition:.2s ease}
.eg-v11 .eg11-btn:hover{transform:translateY(-1px)}
.eg-v11 .eg11-btn svg{width:17px;height:17px}
.eg-v11 .eg11-btn--red{background:var(--eg11-red);color:#fff}
.eg-v11 .eg11-btn--red:hover{background:var(--eg11-red-dark);color:#fff}
.eg-v11 .eg11-btn--wa{background:var(--eg11-green);color:#fff}
.eg-v11 .eg11-btn--ghost{border-color:#555;background:transparent;color:#fff}
.eg-v11 .eg11-trust{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:13px}
.eg-v11 .eg11-trust span{padding:8px 7px;border-radius:9px;background:#292929;color:#d9d9d9;text-align:center;font-size:10px;font-weight:700}

/* STICKY NAV */
.eg-v11 .eg11-nav-wrap{position:sticky;top:68px;z-index:25;margin-bottom:12px}
.eg-v11 .eg11-nav{display:flex;gap:7px;overflow-x:auto;padding:9px;border:1px solid rgba(225,225,221,.92);border-radius:15px;background:rgba(255,255,255,.92);backdrop-filter:blur(16px);box-shadow:0 10px 35px rgba(20,20,20,.05);scrollbar-width:none}
.eg-v11 .eg11-nav::-webkit-scrollbar{display:none}
.eg-v11 .eg11-nav a{flex:0 0 auto;padding:9px 13px;border-radius:10px;color:#4b4b4d;font-size:12px;font-weight:800;white-space:nowrap}
.eg-v11 .eg11-nav a:hover{background:#f2f2ef}
.eg-v11 .eg11-nav a.is-active{background:var(--eg11-ink);color:#fff}

/* SECTION SYSTEM */
.eg-v11 .eg11-section{padding:72px 0}
.eg-v11 .eg11-section+.eg11-section{border-top:1px solid #efefec}
.eg-v11 .eg11-section-head{display:grid;grid-template-columns:minmax(0,.72fr) minmax(280px,.28fr);gap:38px;align-items:end;margin-bottom:30px}
.eg-v11 .eg11-section-head h2{margin:0;color:var(--eg11-ink);font-size:clamp(31px,3.2vw,46px);line-height:1.08;letter-spacing:-.035em}
.eg-v11 .eg11-section-head p{margin:0;color:var(--eg11-muted);font-size:14px;line-height:1.75}
.eg-v11 .eg11-label{display:block;margin-bottom:9px;color:var(--eg11-red);font-size:10px;font-weight:900;letter-spacing:.13em;text-transform:uppercase}

/* PLAN */
.eg-v11 .eg11-plan{display:grid;grid-template-columns:minmax(0,1.18fr) minmax(330px,.82fr);gap:24px}
.eg-v11 .eg11-plan-media{position:relative;overflow:hidden;border-radius:24px;background:var(--eg11-soft);border:1px solid var(--eg11-line)}
.eg-v11 .eg11-plan-media img{display:block;width:100%;min-height:520px;object-fit:contain;background:#fff}
.eg-v11 .eg11-plan-media--multi{overflow:visible;background:transparent;border:0}
.eg-v11 .eg11-floorplan-stack{display:grid;grid-template-columns:1fr;gap:16px;width:100%}
.eg-v11 .eg11-floorplan-stack--multi{grid-template-columns:repeat(2,minmax(0,1fr))}
.eg-v11 .eg11-floorplan-item{margin:0;padding:14px;border:1px solid var(--eg11-line);border-radius:20px;background:#fff;overflow:hidden}
.eg-v11 .eg11-floorplan-caption{margin:0 0 10px;color:var(--eg11-ink);font-size:13px;font-weight:900;letter-spacing:.02em}
.eg-v11 .eg11-floorplan-item a{display:block}
.eg-v11 .eg11-floorplan-item img{display:block;width:100%;height:auto;min-height:0;max-height:620px;object-fit:contain;border-radius:12px;background:#fff}
@media(max-width:767px){
  .eg-v11 .eg11-floorplan-stack--multi{grid-template-columns:1fr}
  .eg-v11 .eg11-floorplan-item{padding:10px;border-radius:16px}
}

.eg-v11 .eg11-plan-placeholder{display:grid;place-items:center;min-height:520px;padding:40px;text-align:center;background:
 linear-gradient(90deg,transparent 49.5%,#e8e8e4 50%,transparent 50.5%),
 linear-gradient(transparent 49.5%,#e8e8e4 50%,transparent 50.5%),#f7f7f4;background-size:72px 72px}
.eg-v11 .eg11-plan-placeholder svg{width:72px;height:72px;color:#aaa;margin-bottom:15px}
.eg-v11 .eg11-plan-placeholder strong{display:block;font-size:22px;color:#444}
.eg-v11 .eg11-plan-placeholder span{display:block;margin-top:5px;color:#888;font-size:13px}
.eg-v11 .eg11-plan-panel{padding:30px;border-radius:24px;background:var(--eg11-ink);color:#fff}
.eg-v11 .eg11-plan-panel h3{margin:0 0 8px;font-size:27px;color:#fff}
.eg-v11 .eg11-plan-panel>p{margin:0 0 22px;color:#c7c7c7}
.eg-v11 .eg11-plan-list{display:grid;gap:0;border-top:1px solid #383838}
.eg-v11 .eg11-plan-row{display:flex;justify-content:space-between;gap:20px;padding:14px 0;border-bottom:1px solid #383838}
.eg-v11 .eg11-plan-row span{color:#a9a9aa}
.eg-v11 .eg11-plan-row strong{text-align:right}

/* HIGHLIGHTS */
.eg-v11 .eg11-highlight-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:13px}
.eg-v11 .eg11-highlight{position:relative;min-height:160px;padding:24px;border:1px solid var(--eg11-line);border-radius:20px;background:#fff;overflow:hidden}
.eg-v11 .eg11-highlight:before{content:"";position:absolute;right:-35px;top:-35px;width:100px;height:100px;border-radius:50%;background:rgba(215,25,32,.045)}
.eg-v11 .eg11-highlight svg{width:28px;height:28px;color:var(--eg11-red);margin-bottom:25px}
.eg-v11 .eg11-highlight small{display:block;margin-bottom:4px;color:#888;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.08em}
.eg-v11 .eg11-highlight strong{display:block;color:#222;font-size:18px;line-height:1.35}

/* EDITORIAL */
.eg-v11 .eg11-editorial{display:grid;grid-template-columns:minmax(260px,.35fr) minmax(0,.65fr);gap:70px;align-items:start}
.eg-v11 .eg11-editorial__title{position:sticky;top:150px}
.eg-v11 .eg11-editorial__title h2{margin:0;color:var(--eg11-ink);font-size:clamp(31px,3vw,44px);line-height:1.08;letter-spacing:-.035em}
.eg-v11 .eg11-rich{color:#4f5052;font-size:16px;line-height:1.9}
.eg-v11 .eg11-rich>:first-child{margin-top:0}
.eg-v11 .eg11-rich h2{position:relative;margin:40px 0 16px;padding-top:24px;color:var(--eg11-ink);font-size:27px;font-weight:800;line-height:1.22;letter-spacing:-.02em}
.eg-v11 .eg11-rich h2:before{content:"";position:absolute;left:0;top:0;width:54px;height:5px;border-radius:3px;background:linear-gradient(90deg,var(--eg11-red),#f4a126)}
.eg-v11 .eg11-rich>h2:first-child{margin-top:0}
.eg-v11 .eg11-rich h3{margin:22px 0 9px;color:var(--eg11-ink);font-size:19px;line-height:1.3}
.eg-v11 .eg11-rich p{margin:0 0 15px}
.eg-v11 .eg11-rich strong{color:var(--eg11-ink);font-weight:800}
.eg-v11 .eg11-rich a{color:var(--eg11-red);font-weight:700}
.eg-v11 .eg11-rich ul,.eg-v11 .eg11-rich ol{padding-left:20px;margin:0 0 18px}
.eg-v11 .eg11-rich li{margin:7px 0}
.eg-v11 .eg11-rich li::marker{color:var(--eg11-red);font-weight:800}
.eg-v11 .eg11-rich hr{height:1px;margin:28px 0;border:0;background:var(--eg11-line)}
.eg-v11 .eg11-rich blockquote{margin:22px 0;padding:16px 18px;border-left:4px solid #f4a126;border-radius:0 11px 11px 0;background:#fff8ea}

/* SPECS */
.eg-v11 .eg11-spec-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
.eg-v11 .eg11-spec-card{border:1px solid var(--eg11-line);border-radius:20px;background:#fff;overflow:hidden}
.eg-v11 .eg11-spec-card__head{display:flex;align-items:center;gap:11px;padding:19px 20px;background:var(--eg11-soft)}
.eg-v11 .eg11-spec-card__head svg{width:23px;height:23px;color:var(--eg11-red)}
.eg-v11 .eg11-spec-card__head strong{font-size:17px;color:#222}
.eg-v11 .eg11-spec-list{margin:0}
.eg-v11 .eg11-spec-row{display:grid;grid-template-columns:minmax(130px,.75fr) minmax(0,1.25fr);gap:18px;padding:14px 20px;border-top:1px solid #eeeeeb}
.eg-v11 .eg11-spec-row dt{color:#777;font-weight:400}
.eg-v11 .eg11-spec-row dd{margin:0;color:#222;font-weight:800;overflow-wrap:anywhere}

/* COST BAND */
.eg-v11 .eg11-cost{padding:40px;border-radius:28px;background:
 radial-gradient(circle at 88% 10%,rgba(215,25,32,.18),transparent 26%),#1c1c1c;color:#fff}
.eg-v11 .eg11-cost-inner{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr);gap:55px;align-items:center}
.eg-v11 .eg11-cost h2{margin:4px 0 12px;color:#fff;font-size:clamp(30px,3vw,43px);line-height:1.08}
.eg-v11 .eg11-cost p{margin:0;color:#c8c8c8;line-height:1.75}
.eg-v11 .eg11-cost-pills{display:flex;flex-wrap:wrap;gap:8px}
.eg-v11 .eg11-cost-pills span{padding:10px 13px;border:1px solid #454545;border-radius:999px;background:#282828;color:#f0f0f0;font-size:12px;font-weight:700}

/* PROCESS */
.eg-v11 .eg11-timeline{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:0;position:relative}
.eg-v11 .eg11-timeline:before{content:"";position:absolute;left:5%;right:5%;top:25px;height:2px;background:#e3e3df}
.eg-v11 .eg11-step{position:relative;padding:0 12px;text-align:left}
.eg-v11 .eg11-step-num{position:relative;z-index:2;width:50px;height:50px;display:grid;place-items:center;margin-bottom:18px;border-radius:50%;background:#fff;border:2px solid var(--eg11-red);color:var(--eg11-red);font-size:14px;font-weight:900}
.eg-v11 .eg11-step h3{margin:0 0 7px;font-size:15px;line-height:1.3}
.eg-v11 .eg11-step p{margin:0;color:#777;font-size:12px;line-height:1.55}

/* REGIONAL */
.eg-v11 .eg11-regional{display:grid;grid-template-columns:minmax(0,1fr) minmax(300px,.42fr);gap:45px;align-items:center;padding:34px;border-radius:24px;background:var(--eg11-soft)}
.eg-v11 .eg11-regional h2{margin:0 0 10px;font-size:30px;color:#222}
.eg-v11 .eg11-regional p{margin:0;color:#666;line-height:1.75}
.eg-v11 .eg11-regional-box{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.eg-v11 .eg11-regional-box div{padding:17px;border:1px solid var(--eg11-line);border-radius:14px;background:#fff}
.eg-v11 .eg11-regional-box strong{display:block;font-size:15px}
.eg-v11 .eg11-regional-box span{display:block;margin-top:2px;color:#888;font-size:11px}

/* FAQ */
.eg-v11 .eg11-faq{display:grid;gap:9px;max-width:980px}
.eg-v11 .eg11-faq details{border:1px solid var(--eg11-line);border-radius:15px;background:#fff;overflow:hidden}
.eg-v11 .eg11-faq summary{position:relative;padding:19px 54px 19px 20px;cursor:pointer;font-weight:800;list-style:none}
.eg-v11 .eg11-faq summary::-webkit-details-marker{display:none}
.eg-v11 .eg11-faq summary:after{content:"+";position:absolute;right:20px;top:50%;transform:translateY(-50%);font-size:23px;color:#888}
.eg-v11 .eg11-faq details[open] summary:after{content:"−";color:var(--eg11-red)}
.eg-v11 .eg11-faq details[open] summary{background:var(--eg11-soft)}
.eg-v11 .eg11-faq p{margin:0;padding:0 20px 20px;color:#666;line-height:1.75}

/* RELATED */
.eg-v11 .eg11-related-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
.eg-v11 .eg11-related-card{overflow:hidden;border:1px solid var(--eg11-line);border-radius:20px;background:#fff}
.eg-v11 .eg11-related-card img{display:block;width:100%;aspect-ratio:4/3;object-fit:cover;background:#f4f4f2}
.eg-v11 .eg11-related-card__body{padding:18px}
.eg-v11 .eg11-related-card h3{margin:0 0 8px;font-size:18px}
.eg-v11 .eg11-related-card h3 a{color:#222}
.eg-v11 .eg11-related-card__link{color:var(--eg11-red);font-size:12px;font-weight:800}

/* LEAD */
.eg-v11 .eg11-lead{display:grid;grid-template-columns:minmax(300px,.42fr) minmax(0,.58fr);gap:44px;padding:42px;border-radius:30px;background:#1c1c1c;color:#fff}
.eg-v11 .eg11-lead-copy{position:relative;overflow:hidden}
.eg-v11 .eg11-lead-copy:after{content:"";position:absolute;right:-80px;bottom:-80px;width:220px;height:220px;border-radius:50%;background:rgba(215,25,32,.11)}
.eg-v11 .eg11-lead h2{margin:6px 0 14px;color:#fff;font-size:36px;line-height:1.08}
.eg-v11 .eg11-lead-copy>p{margin:0;color:#c8c8c8;line-height:1.8;max-width:430px}
.eg-v11 .eg11-lead-product{display:grid;grid-template-columns:92px 1fr;gap:14px;align-items:center;margin-top:24px;padding:12px;border:1px solid #3c3c3c;border-radius:16px;background:#252525}
.eg-v11 .eg11-lead-product img{width:92px;height:72px;object-fit:cover;border-radius:11px;background:#333}
.eg-v11 .eg11-lead-product__placeholder{width:92px;height:72px;display:grid;place-items:center;border-radius:11px;background:#333;color:#777}
.eg-v11 .eg11-lead-product small{display:block;color:#9f9f9f}
.eg-v11 .eg11-lead-product strong{display:block;color:#fff;line-height:1.35}
.eg-v11 .eg-lead-box__intro h2{font-size:27px!important}
.eg-v11 .eg-form-grid{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:14px!important}
.eg-v11 .eg-field--full{grid-column:1/-1!important}
.eg-v11 .eg-field label,.eg-v11 .eg-field__label{display:block;margin:0 0 6px;color:#eee;font-size:12px;font-weight:700}
.eg-v11 .eg-field input[type=text],.eg-v11 .eg-field input[type=tel],.eg-v11 .eg-field input[type=email],.eg-v11 .eg-field select,.eg-v11 .eg-field textarea{
 display:block;width:100%;min-height:46px;margin:0;padding:11px 12px;border:1px solid #494949;border-radius:10px;background:#fff;color:#222;font:inherit;outline:none
}
.eg-v11 .eg-field textarea{min-height:115px}
.eg-v11 .eg-choice-row{display:grid!important;grid-template-columns:1fr 1fr!important;gap:10px!important}
.eg-v11 .eg-choice{position:relative;display:block!important;margin:0;padding:13px 14px;border:1px solid #4a4a4a;border-radius:10px;background:#292929;cursor:pointer}
.eg-v11 .eg-choice input{position:absolute;opacity:0;pointer-events:none}
.eg-v11 .eg-choice span{display:block;color:#fff;font-weight:800}
.eg-v11 .eg-choice:has(input:checked){border-color:var(--eg11-red);background:#361d1f;box-shadow:0 0 0 1px var(--eg11-red) inset}
.eg-v11 .eg-corporate-field[hidden]{display:none!important}
.eg-v11 .eg-consent{display:flex!important;gap:9px;align-items:flex-start;color:#d1d1d1!important;font-size:11px!important;font-weight:400!important}
.eg-v11 .eg-consent input{width:auto!important;min-height:0!important;margin-top:3px}
.eg-v11 .eg-form-actions{display:flex;align-items:center;gap:14px;margin-top:16px}
.eg-v11 .eg-form-actions .eg-btn{border:0;cursor:pointer}
.eg-v11 .eg-hp-field{position:absolute!important;left:-9999px!important;width:1px!important;height:1px!important;overflow:hidden!important}

/* LIGHTBOX */
.eg-v11 .eg11-lightbox{position:fixed;inset:0;z-index:99999;display:none;align-items:center;justify-content:center;padding:30px;background:rgba(0,0,0,.92)}
.eg-v11 .eg11-lightbox.is-open{display:flex}
.eg-v11 .eg11-lightbox img{max-width:min(1400px,94vw);max-height:88vh;object-fit:contain}
.eg-v11 .eg11-lightbox button{position:absolute;right:24px;top:20px;width:46px;height:46px;border:0;border-radius:50%;background:#fff;color:#222;font-size:25px;cursor:pointer}

/* MOBILE BAR */
.eg-v11 .eg11-mobile-bar{display:none}

/* RESPONSIVE */

/* =========================
   EGESER V11.1 FINAL POLISH
   ========================= */
@media(min-width:1101px){
  .eg-v11 .eg11-hero{
    grid-template-columns:minmax(0,1.14fr) minmax(430px,.86fr);
    gap:46px;
    padding-top:24px;
    padding-bottom:46px;
  }
  .eg-v11 .eg11-main-media{aspect-ratio:4/3}
  .eg-v11 .eg11-summary{
    padding-top:8px;
  }
  .eg-v11 h1{
    font-size:clamp(46px,3.25vw,52px);
    line-height:1.01;
    letter-spacing:-.038em;
    max-width:610px;
  }
  .eg-v11 .eg11-summary__intro{
    font-size:15px;
    line-height:1.72;
    margin-bottom:20px;
  }
  .eg-v11 .eg11-keyfacts{
    gap:8px;
    margin-bottom:15px;
  }
  .eg-v11 .eg11-keyfact{
    padding:12px 12px;
  }
  .eg-v11 .eg11-keyfact svg{
    width:18px;
    height:18px;
    margin-bottom:7px;
  }
  .eg-v11 .eg11-quote-card{
    padding:17px 17px 15px;
  }
  .eg-v11 .eg11-quote-card__top strong{
    font-size:17px;
  }
  .eg-v11 .eg11-quote-card__top span{
    font-size:11px;
  }
  .eg-v11 .eg11-actions{
    margin-top:13px;
  }
  .eg-v11 .eg11-btn{
    min-height:44px;
  }
  .eg-v11 .eg11-trust span{
    padding:9px 7px;
    font-size:11px;
    letter-spacing:.01em;
  }
}
.eg-v11 .eg11-nav-wrap{
  transition:box-shadow .2s ease, transform .2s ease;
}
.eg-v11 .eg11-nav-wrap.is-stuck .eg11-nav{
  box-shadow:0 14px 34px rgba(20,20,20,.10);
  border-color:#ddd;
}
.eg-v11 .eg11-nav a{
  transition:background .2s ease,color .2s ease,transform .2s ease;
}
.eg-v11 .eg11-nav a:hover{
  transform:translateY(-1px);
}
.eg-v11 .eg11-nav a.is-active{
  background:var(--eg11-red);
  color:#fff;
}
@media(max-width:640px){
  .eg-v11 h1{
    font-size:34px;
    line-height:1.04;
    letter-spacing:-.035em;
  }
  .eg-v11 .eg11-summary__intro{
    font-size:14px;
    line-height:1.65;
  }
  .eg-v11 .eg11-keyfact strong{
    font-size:13px;
  }
  .eg-v11 .eg11-nav{
    padding:7px;
  }
  .eg-v11 .eg11-nav a{
    padding:8px 11px;
    font-size:11px;
  }
}

@media(max-width:1100px){
 .eg-v11 .eg11-hero{grid-template-columns:1fr;gap:30px}
 .eg-v11 .eg11-summary{position:static}
 .eg-v11 .eg11-main-media__placeholder{min-height:470px}
 .eg-v11 .eg11-plan{grid-template-columns:1fr}
 .eg-v11 .eg11-highlight-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
 .eg-v11 .eg11-timeline{grid-template-columns:repeat(3,1fr);gap:26px 0}
 .eg-v11 .eg11-timeline:before{display:none}
 .eg-v11 .eg11-regional{grid-template-columns:1fr}
}
@media(max-width:820px){
 .eg-v11 .eg11-container{width:min(calc(100% - 28px),1240px)}
 .eg-v11 .eg11-keyfacts{grid-template-columns:repeat(2,1fr)}
 .eg-v11 .eg11-actions{grid-template-columns:1fr}
 .eg-v11 .eg11-section{padding:52px 0}
 .eg-v11 .eg11-section-head{grid-template-columns:1fr;gap:12px}
 .eg-v11 .eg11-editorial{grid-template-columns:1fr;gap:24px}
 .eg-v11 .eg11-editorial__title{position:static}
 .eg-v11 .eg11-rich h2{font-size:22px}
 .eg-v11 .eg11-rich h3{font-size:17px}
 .eg-v11 .eg11-spec-grid{grid-template-columns:1fr}
 .eg-v11 .eg11-cost-inner{grid-template-columns:1fr;gap:25px}
 .eg-v11 .eg11-related-grid{grid-template-columns:1fr}
 .eg-v11 .eg11-lead{grid-template-columns:1fr;padding:26px 20px}
 .eg-v11 .eg11-form-grid{grid-template-columns:1fr!important}
}
@media(max-width:640px){
 .eg-v11{padding-bottom:78px}
 .eg-v11 h1{font-size:40px}
 .eg-v11 .eg11-main-media{border-radius:20px}
 .eg-v11 .eg11-main-media__placeholder{min-height:330px}
 .eg-v11 .eg11-thumbs{grid-template-columns:repeat(4,1fr)}
 .eg-v11 .eg11-thumb img{height:70px}
 .eg-v11 .eg11-keyfacts{grid-template-columns:1fr 1fr}
 .eg-v11 .eg11-highlight-grid{grid-template-columns:1fr}
 .eg-v11 .eg11-plan-media img,.eg-v11 .eg11-plan-placeholder{min-height:350px}
 .eg-v11 .eg11-timeline{grid-template-columns:1fr}
 .eg-v11 .eg11-step{display:grid;grid-template-columns:50px 1fr;gap:14px}
 .eg-v11 .eg11-step-num{grid-row:1/3;margin:0}
 .eg-v11 .eg11-step h3{align-self:end}
 .eg-v11 .eg11-regional-box{grid-template-columns:1fr}
 .eg-v11 .eg11-lead h2{font-size:30px}
 .eg-v11 .eg-form-grid{grid-template-columns:1fr!important}
 .eg-v11 .eg-field--full{grid-column:auto!important}
 .eg-v11 .eg11-mobile-bar{position:fixed;left:0;right:0;bottom:0;z-index:9998;display:grid;grid-template-columns:1fr 1fr 1.2fr;gap:1px;padding:7px;background:#171717;box-shadow:0 -8px 30px rgba(0,0,0,.18)}
 .eg-v11 .eg11-mobile-bar a{display:flex;align-items:center;justify-content:center;min-height:48px;border-radius:8px;color:#fff;font-size:12px;font-weight:900}
 .eg-v11 .eg11-mobile-bar .wa{background:#225b53}
 .eg-v11 .eg11-mobile-bar .quote{background:var(--eg11-red)}
 .eg-v11 .eg11-nav-wrap{top:62px}
}
</style>

<main id="content" class="eg-v11">
  <div class="eg11-container">
    <nav class="eg11-breadcrumb" aria-label="Breadcrumb">
      <ol>
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ol>
    </nav>

    <?php echo $content_top; ?>

    <section class="eg11-hero" id="eg11-top" aria-labelledby="eg11-title">
      <div class="eg11-gallery">
        <?php if ($thumb) { ?>
          <div class="eg11-main-media">
            <span class="eg11-media-badge">MODEL GÖRSELİ</span>
            <img id="eg11-main-img" src="<?php echo $thumb; ?>" data-popup="<?php echo $popup; ?>" alt="<?php echo htmlspecialchars(strip_tags($heading_title), ENT_QUOTES, 'UTF-8'); ?>" width="900" height="675" fetchpriority="high" decoding="async">
            <button type="button" class="eg11-media-zoom" id="eg11-zoom" aria-label="Görseli büyüt">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5M11 8v6M8 11h6"></path></svg>
            </button>
          </div>
        <?php } else { ?>
          <div class="eg11-main-media eg11-main-media__placeholder">
            <div>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18M5 21V10l7-6 7 6v11M9 21v-6h6v6"></path></svg>
              <strong>Ürün görseli hazırlanıyor</strong>
            </div>
          </div>
        <?php } ?>

        <?php if ($thumb || $eg_gallery_images) { ?>
        <div class="eg11-thumbs" aria-label="Ürün görsel galerisi">
          <?php if ($thumb) { ?>
            <button type="button" class="eg11-thumb is-active" data-src="<?php echo $thumb; ?>" data-popup="<?php echo $popup; ?>">
              <img src="<?php echo $thumb; ?>" alt="<?php echo htmlspecialchars(strip_tags($heading_title), ENT_QUOTES, 'UTF-8'); ?> ana görsel" loading="lazy">
            </button>
          <?php } ?>
          <?php foreach ($eg_gallery_images as $eg_img) { ?>
            <button type="button" class="eg11-thumb" data-src="<?php echo $eg_img['thumb']; ?>" data-popup="<?php echo $eg_img['popup']; ?>">
              <img src="<?php echo $eg_img['thumb']; ?>" alt="<?php echo htmlspecialchars(strip_tags($heading_title), ENT_QUOTES, 'UTF-8'); ?> detay görseli" loading="lazy">
            </button>
          <?php } ?>
        </div>
        <?php } ?>
      </div>

      <aside class="eg11-summary">
        <span class="eg11-kicker">PREFABRİK EV MODELİ</span>
        <h1 id="eg11-title"><?php echo $heading_title; ?></h1>
        <p class="eg11-summary__intro"><?php echo htmlspecialchars($eg_short_desc, ENT_QUOTES, 'UTF-8'); ?></p>

        <div class="eg11-keyfacts" aria-label="Model özeti">
          <?php if ($eg_area !== '') { ?>
          <div class="eg11-keyfact">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h6M4 4v6M20 4h-6M20 4v6M4 20h6M4 20v-6M20 20h-6M20 20v-6"></path></svg>
            <small>Alan</small><strong><?php echo htmlspecialchars($eg_area, ENT_QUOTES, 'UTF-8'); ?></strong>
          </div>
          <?php } ?>
          <?php if ($eg_rooms !== '') { ?>
          <div class="eg11-keyfact">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 11h18M5 11V6h5v5M14 11V7h5v4M4 11v7M20 11v7"></path></svg>
            <small>Plan</small><strong><?php echo htmlspecialchars($eg_rooms, ENT_QUOTES, 'UTF-8'); ?></strong>
          </div>
          <?php } ?>
          <?php if ($eg_floor !== '') { ?>
          <div class="eg11-keyfact">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20h16M6 20V8l6-4 6 4v12M9 12h6M9 16h6"></path></svg>
            <small>Kat</small><strong><?php echo htmlspecialchars($eg_floor, ENT_QUOTES, 'UTF-8'); ?></strong>
          </div>
          <?php } ?>
          <div class="eg11-keyfact">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 21h18M5 21V10l7-6 7 6v11M9 21v-6h6v6"></path></svg>
            <small>Yapı Tipi</small><strong><?php echo htmlspecialchars($eg_type, ENT_QUOTES, 'UTF-8'); ?></strong>
          </div>
        </div>

        <div class="eg11-quote-card">
          <div class="eg11-quote-card__top">
            <div>
              <?php if ($eg_has_real_price) { ?>
                <strong>Güncel satış fiyatı</strong>
                <span>Seçenekler ve uygulama kapsamına göre netleşebilir.</span>
              <?php } else { ?>
                <strong>Bu model için size özel teklif hazırlayalım</strong>
                <span>Kurulum yeri, teknik kapsam ve opsiyonları birlikte değerlendirelim.</span>
              <?php } ?>
            </div>
            <?php if ($eg_has_real_price) { ?>
              <div class="eg11-price"><?php echo $special ? $special : $price; ?></div>
            <?php } ?>
          </div>

          <div class="eg11-actions">
            <a class="eg11-btn eg11-btn--red" href="#eg-lead">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4zM7 8h10M7 12h7M7 16h5"></path></svg>
              Teklif Al
            </a>
            <a class="eg11-btn eg11-btn--wa" href="<?php echo $eg_wa_href; ?>" target="_blank" rel="noopener noreferrer" data-wa-phone="<?php echo htmlspecialchars($eg_wa_phone, ENT_QUOTES, 'UTF-8'); ?>" data-placement="product-v11" data-entity-type="product" data-entity-id="<?php echo (int)$egeser_product_id; ?>" data-wa-message="<?php echo htmlspecialchars($eg_wa_message, ENT_QUOTES, 'UTF-8'); ?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11.5a8 8 0 0 1-11.7 7L4 20l1.5-4.1A8 8 0 1 1 20 11.5z"></path></svg>
              WhatsApp
            </a>
            <a class="eg11-btn eg11-btn--ghost" href="<?php echo $eg_tel_href; ?>" data-placement="product-v11" data-entity-type="product" data-entity-id="<?php echo (int)$egeser_product_id; ?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.8 2.1z"></path></svg>
              Ara
            </a>
          </div>

          <div class="eg11-trust">
            <span>Projelendirme</span><span>Üretim</span><span>Sevkiyat</span><span>Montaj</span>
          </div>
        </div>
      </aside>
    </section>

    <div class="eg11-nav-wrap">
      <nav class="eg11-nav" aria-label="Ürün detay bölümleri">
        <a href="#eg-plan">Kat Planı</a>
        <a href="#eg-highlights">Öne Çıkanlar</a>
        <a href="#eg-tech">Teknik Özellikler</a>
        <a href="#eg-process">Proje Süreci</a>
        <a href="#eg-faq">SSS</a>
        <a href="#eg-lead">Teklif Al</a>
      </nav>
    </div>

    <section class="eg11-section" id="eg-plan">
      <div class="eg11-section-head">
        <div><span class="eg11-label">KAT PLANI</span><h2>Alanı nasıl kullanacağınızı ilk bakışta görün.</h2></div>
        <p>Plan görseli, oda kurgusu ve modelin temel ölçü bilgileri aynı bölümde değerlendirilir.</p>
      </div>

      <div class="eg11-plan">
        <div class="eg11-plan-media<?php echo count($eg_floor_plans) > 1 ? ' eg11-plan-media--multi' : ''; ?>">
          <?php if (!empty($eg_floor_plans)) { ?>
            <div class="eg11-floorplan-stack<?php echo count($eg_floor_plans) > 1 ? ' eg11-floorplan-stack--multi' : ''; ?>">
              <?php foreach ($eg_floor_plans as $eg_floor_index => $eg_floor_plan) { ?>
                <figure class="eg11-floorplan-item">
                  <?php if (!empty($eg_floor_plan['eg_plan_label'])) { ?>
                    <figcaption class="eg11-floorplan-caption"><?php echo htmlspecialchars($eg_floor_plan['eg_plan_label'], ENT_QUOTES, 'UTF-8'); ?></figcaption>
                  <?php } ?>
                  <?php if (!empty($eg_floor_plan['popup'])) { ?>
                    <a href="<?php echo $eg_floor_plan['popup']; ?>" title="<?php echo htmlspecialchars($eg_floor_plan['eg_plan_label'], ENT_QUOTES, 'UTF-8'); ?>">
                  <?php } ?>
                      <img
                        src="<?php echo !empty($eg_floor_plan['thumb']) ? $eg_floor_plan['thumb'] : $eg_floor_plan['popup']; ?>"
                        alt="<?php echo htmlspecialchars(strip_tags($heading_title) . ' - ' . $eg_floor_plan['eg_plan_label'], ENT_QUOTES, 'UTF-8'); ?>"
                        loading="<?php echo $eg_floor_index === 0 ? 'eager' : 'lazy'; ?>"
                        decoding="async">
                  <?php if (!empty($eg_floor_plan['popup'])) { ?></a><?php } ?>
                </figure>
              <?php } ?>
            </div>
          <?php } else { ?>
            <div class="eg11-plan-placeholder">
              <div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16v16H4zM4 11h7V4M11 20v-5h9M11 11h9"></path></svg>
                <strong>Kat planı görseli hazırlanıyor</strong>
                <span>Plan bilgileri model özellikleriyle birlikte aşağıda yer alır.</span>
              </div>
            </div>
          <?php } ?>
        </div>

        <div class="eg11-plan-panel">
          <span class="eg11-label">MODEL ÖZETİ</span>
          <h3><?php echo $heading_title; ?></h3>
          <p>Kullanım senaryosu ve saha koşullarına göre plan değerlendirmesi yapılabilir.</p>
          <div class="eg11-plan-list">
            <?php if ($eg_area !== '') { ?><div class="eg11-plan-row"><span>Toplam alan</span><strong><?php echo htmlspecialchars($eg_area, ENT_QUOTES, 'UTF-8'); ?></strong></div><?php } ?>
            <?php if ($eg_rooms !== '') { ?><div class="eg11-plan-row"><span>Oda planı</span><strong><?php echo htmlspecialchars($eg_rooms, ENT_QUOTES, 'UTF-8'); ?></strong></div><?php } ?>
            <?php if ($eg_floor !== '') { ?><div class="eg11-plan-row"><span>Kat sayısı</span><strong><?php echo htmlspecialchars($eg_floor, ENT_QUOTES, 'UTF-8'); ?></strong></div><?php } ?>
            <div class="eg11-plan-row"><span>Yapı tipi</span><strong><?php echo htmlspecialchars($eg_type, ENT_QUOTES, 'UTF-8'); ?></strong></div>
            <?php if ($eg_veranda !== '') { ?><div class="eg11-plan-row"><span>Veranda</span><strong><?php echo htmlspecialchars($eg_veranda, ENT_QUOTES, 'UTF-8'); ?></strong></div><?php } ?>
            <?php if ($model) { ?><div class="eg11-plan-row"><span>Model kodu</span><strong><?php echo htmlspecialchars($model, ENT_QUOTES, 'UTF-8'); ?></strong></div><?php } ?>
          </div>
        </div>
      </div>
    </section>

    <?php if (!empty($eg_highlights)) { ?>
    <section class="eg11-section" id="eg-highlights">
      <div class="eg11-section-head">
        <div><span class="eg11-label">BU MODELDE NELER VAR?</span><h2>Yaşam kalitesini belirleyen detaylar.</h2></div>
        <p>Ürüne girilen teknik bilgiler içinden kullanıcı kararını hızlandıran öne çıkan özellikler.</p>
      </div>
      <div class="eg11-highlight-grid">
        <?php foreach ($eg_highlights as $eg_highlight) { ?>
        <article class="eg11-highlight">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 4h16v16H4zM8 8h8v8H8z"></path></svg>
          <small><?php echo htmlspecialchars($eg_highlight['label'], ENT_QUOTES, 'UTF-8'); ?></small>
          <strong><?php echo htmlspecialchars($eg_highlight['value'], ENT_QUOTES, 'UTF-8'); ?></strong>
        </article>
        <?php } ?>
      </div>
    </section>
    <?php } ?>

    <section class="eg11-section">
      <div class="eg11-editorial">
        <div class="eg11-editorial__title">
          <span class="eg11-label">MODEL DETAYI</span>
          <h2><?php echo $heading_title; ?> hakkında</h2>
        </div>
        <div class="eg11-rich"><?php echo $description; ?></div>
      </div>
    </section>

    <?php if (!empty($eg_spec_buckets)) { ?>
    <section class="eg11-section" id="eg-tech">
      <div class="eg11-section-head">
        <div><span class="eg11-label">TEKNİK ÖZELLİKLER</span><h2>Teknik kapsamı kategori kategori inceleyin.</h2></div>
        <p>Ürün kartına girilen değerler otomatik olarak doğru teknik başlık altında gruplanır.</p>
      </div>

      <div class="eg11-spec-grid">
        <?php foreach ($eg_spec_buckets as $eg_bucket_name => $eg_bucket_items) { ?>
        <article class="eg11-spec-card">
          <div class="eg11-spec-card__head">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16M4 12h16M4 19h16"></path></svg>
            <strong><?php echo htmlspecialchars($eg_bucket_name, ENT_QUOTES, 'UTF-8'); ?></strong>
          </div>
          <dl class="eg11-spec-list">
            <?php foreach ($eg_bucket_items as $eg_item) { ?>
            <div class="eg11-spec-row">
              <dt><?php echo $eg_item['name']; ?></dt>
              <dd><?php echo $eg_item['text']; ?></dd>
            </div>
            <?php } ?>
          </dl>
        </article>
        <?php } ?>
      </div>
    </section>
    <?php } ?>

    <section class="eg11-section" id="eg-cost">
      <div class="eg11-cost">
        <div class="eg11-cost-inner">
          <div>
            <span class="eg11-label"><?php echo htmlspecialchars($eg_pricing_eyebrow, ENT_QUOTES, 'UTF-8'); ?></span>
            <h2>Bu evin size özel maliyetini birlikte netleştirelim.</h2>
            <p>Prefabrik yapı fiyatı yalnızca m² üzerinden hesaplanmaz. Plan, teknik kapsam, lokasyon, sevkiyat ve montaj koşulları toplam maliyeti etkiler.</p>
            <div style="margin-top:20px"><a class="eg11-btn eg11-btn--red" href="#eg-lead">Proje Teklifi Al</a></div>
          </div>
          <div class="eg11-cost-pills">
            <span>Toplam m²</span><span>Oda Planı</span><span>Duvar & Çatı</span><span>Yalıtım</span><span>Doğrama</span><span>Kurulum Bölgesi</span><span>Sevkiyat</span><span>Montaj</span><span>Opsiyonlar</span>
          </div>
        </div>
      </div>
    </section>

    <section class="eg11-section" id="eg-process">
      <div class="eg11-section-head">
        <div><span class="eg11-label">PROJE SÜRECİ</span><h2>Fikirden teslime, 6 net adım.</h2></div>
        <p>Teklif öncesi ihtiyaç analiziyle başlayan süreç, üretim ve montaj kontrolleriyle tamamlanır.</p>
      </div>
      <div class="eg11-timeline">
        <article class="eg11-step"><div class="eg11-step-num">01</div><h3>İhtiyaç Analizi</h3><p>Kullanım amacı, m², oda ihtiyacı ve lokasyon belirlenir.</p></article>
        <article class="eg11-step"><div class="eg11-step-num">02</div><h3>Plan Değerlendirmesi</h3><p>Mevcut model veya ihtiyaca uygun plan değerlendirilir.</p></article>
        <article class="eg11-step"><div class="eg11-step-num">03</div><h3>Teknik Kapsam</h3><p>Yapı, yalıtım, doğrama ve tesisat detayları netleşir.</p></article>
        <article class="eg11-step"><div class="eg11-step-num">04</div><h3>Teklif & Onay</h3><p>Netleşen kapsam doğrultusunda teklif hazırlanır.</p></article>
        <article class="eg11-step"><div class="eg11-step-num">05</div><h3>Üretim & Sevkiyat</h3><p>Yapı elemanları hazırlanır ve sahaya sevk edilir.</p></article>
        <article class="eg11-step"><div class="eg11-step-num">06</div><h3>Montaj & Teslim</h3><p>Montaj tamamlanır ve teslim öncesi kontroller yapılır.</p></article>
      </div>
    </section>

    <section class="eg11-section">
      <div class="eg11-regional">
        <div>
          <span class="eg11-label">BÖLGESEL UYGULAMA</span>
          <h2><?php echo htmlspecialchars($eg_region_title, ENT_QUOTES, 'UTF-8'); ?></h2>
          <p>Kurulum bölgesinin saha erişimi, zemin durumu, sevkiyat güzergâhı ve montaj alanı proje başlangıcında değerlendirilir. Aynı model farklı sahalarda farklı uygulama koşulları gerektirebilir.</p>
        </div>
        <div class="eg11-regional-box">
          <div><strong>Saha Erişimi</strong><span>Vinç, sevkiyat ve montaj erişimi</span></div>
          <div><strong>Zemin</strong><span>Uygulama öncesi hazırlık koşulları</span></div>
          <div><strong>Sevkiyat</strong><span>Güzergâh ve lojistik değerlendirmesi</span></div>
          <div><strong>Bağlantılar</strong><span>Elektrik ve sıhhi tesisat ihtiyaçları</span></div>
        </div>
      </div>
    </section>

    <section class="eg11-section" id="eg-faq">
      <div class="eg11-section-head">
        <div><span class="eg11-label">SIK SORULAN SORULAR</span><h2><?php echo $heading_title; ?> hakkında merak edilenler.</h2></div>
        <p>Teklif öncesinde en çok sorulan başlıkları kısa ve net yanıtlarla topladık.</p>
      </div>
      <div class="eg11-faq">
        <details><summary>Bu modelin planı değiştirilebilir mi?</summary><p>Plan değişikliği; taşıyıcı sistem, tesisat, cephe ve üretim koşulları birlikte değerlendirilerek proje aşamasında netleştirilir.</p></details>
        <details><summary>Bu modelin fiyatını hangi unsurlar etkiler?</summary><p>Toplam alan, plan, teknik kapsam, kurulum bölgesi, sevkiyat, montaj koşulları ve özel talepler fiyat üzerinde etkili olabilir.</p></details>
        <details><summary>Prefabrik yapı için zemin hazırlığı gerekir mi?</summary><p>Yapı oturum alanı ve zemin hazırlığı proje ve saha koşullarına göre belirlenir. Uygulama öncesinde saha uygunluğunun değerlendirilmesi gerekir.</p></details>
        <details><summary>Teklif almak için hangi bilgiler gerekli?</summary><p>Kullanım amacı, yaklaşık m², tercih edilen oda/bölüm yapısı, kurulum lokasyonu ve iletişim bilgileri ilk değerlendirme için yeterlidir.</p></details>
        <details><summary>Sevkiyat ve montaj süreci nasıl belirlenir?</summary><p>Üretim kapsamı, proje lokasyonu, saha erişimi ve uygulama programına göre sevkiyat ve montaj planı teklif aşamasında netleştirilir.</p></details>
      </div>
    </section>

    <?php if ($products) { ?>
    <section class="eg11-section">
      <div class="eg11-section-head">
        <div><span class="eg11-label">BENZER MODELLER</span><h2><?php echo $eg_is_corporate ? 'Alternatif prefabrik yapı çözümlerini karşılaştırın.' : 'Alternatif prefabrik ev modellerini karşılaştırın.'; ?></h2></div>
        <p>Yakın m² veya oda planındaki diğer ürünleri inceleyerek doğru modeli daha hızlı seçin.</p>
      </div>
      <div class="eg11-related-grid">
        <?php foreach ($products as $product) { ?>
        <article class="eg11-related-card">
          <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo htmlspecialchars(strip_tags($product['name']), ENT_QUOTES, 'UTF-8'); ?>" loading="lazy"></a>
          <div class="eg11-related-card__body">
            <h3><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h3>
            <a class="eg11-related-card__link" href="<?php echo $product['href']; ?>">Modeli İncele →</a>
          </div>
        </article>
        <?php } ?>
      </div>
    </section>
    <?php } ?>

    <section class="eg11-section" id="eg-lead">
      <div class="eg11-lead">
        <div class="eg11-lead-copy">
          <span class="eg11-label">PROJE TEKLİFİ</span>
          <h2>Bu model için projenize özel teklif alın.</h2>
          <p>Kurulum yerinizi ve ihtiyacınızı paylaşın. Satış ekibi ürün ve proje kapsamına göre sizinle iletişime geçsin.</p>

          <div class="eg11-lead-product">
            <?php if ($thumb) { ?>
              <img src="<?php echo $thumb; ?>" alt="<?php echo htmlspecialchars(strip_tags($heading_title), ENT_QUOTES, 'UTF-8'); ?>">
            <?php } else { ?>
              <div class="eg11-lead-product__placeholder">EV</div>
            <?php } ?>
            <div><small>Teklif alınan model</small><strong><?php echo $heading_title; ?></strong></div>
          </div>
        </div>

        <div>
          <?php
            $eg_form_context = 'product-v11';
            $eg_form_default_customer_type = $eg_is_corporate ? 'Kurumsal' : 'Bireysel';
            $eg_form_title = 'Teklif Bilgilerinizi Gönderin';
            $eg_form_product_id = isset($egeser_product_id) ? (int)$egeser_product_id : 0;
            $eg_form_product_name = isset($heading_title) ? $heading_title : '';
            $eg_form_source = isset($heading_title) ? $heading_title : 'Ürün';
            include(DIR_TEMPLATE . 'egeser/template/extension/module/egeser_lead_form.tpl');
          ?>
        </div>
      </div>
    </section>

    <?php echo $content_bottom; ?>
  </div>

  <div class="eg11-lightbox" id="eg11-lightbox" aria-hidden="true">
    <button type="button" id="eg11-lightbox-close" aria-label="Kapat">×</button>
    <img id="eg11-lightbox-img" src="" alt="">
  </div>

  <div class="eg11-mobile-bar" aria-label="Hızlı iletişim">
    <a href="<?php echo $eg_tel_href; ?>" data-placement="product-mobile" data-entity-type="product" data-entity-id="<?php echo (int)$egeser_product_id; ?>">Ara</a>
    <a class="wa" href="<?php echo $eg_wa_href; ?>" target="_blank" rel="noopener noreferrer" data-wa-phone="<?php echo htmlspecialchars($eg_wa_phone, ENT_QUOTES, 'UTF-8'); ?>" data-placement="product-mobile" data-entity-type="product" data-entity-id="<?php echo (int)$egeser_product_id; ?>" data-wa-message="<?php echo htmlspecialchars($eg_wa_message, ENT_QUOTES, 'UTF-8'); ?>">WhatsApp</a>
    <a class="quote" href="#eg-lead">Teklif Al</a>
  </div>
</main>

<?php
$eg_product_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => trim(strip_tags($heading_title)),
    'description' => $eg_clean_desc,
    'url' => $eg_current_url
);
if (!empty($model)) { $eg_product_schema['sku'] = trim(strip_tags($model)); }
if (!empty($manufacturer)) { $eg_product_schema['brand'] = array('@type' => 'Brand', 'name' => trim(strip_tags($manufacturer))); }
if (!empty($eg_image_schema)) { $eg_product_schema['image'] = $eg_image_schema; }
if (!empty($eg_attr_props)) { $eg_product_schema['additionalProperty'] = $eg_attr_props; }
if ($eg_has_real_price && !empty($egeser_schema_price)) {
    $eg_product_schema['offers'] = array(
        '@type' => 'Offer',
        'url' => $eg_current_url,
        'priceCurrency' => isset($currency_code) && $currency_code ? $currency_code : 'TRY',
        'price' => (string)$egeser_schema_price
    );
}

$eg_breadcrumb_schema = array('@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => array());
$eg_pos = 1;
foreach ($breadcrumbs as $eg_bc) {
    $eg_breadcrumb_schema['itemListElement'][] = array('@type' => 'ListItem', 'position' => $eg_pos++, 'name' => trim(strip_tags($eg_bc['text'])), 'item' => $eg_bc['href']);
}

$eg_faq_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array(
        array('@type'=>'Question','name'=>'Bu modelin planı değiştirilebilir mi?','acceptedAnswer'=>array('@type'=>'Answer','text'=>'Plan değişikliği; taşıyıcı sistem, tesisat, cephe ve üretim koşulları birlikte değerlendirilerek proje aşamasında netleştirilir.')),
        array('@type'=>'Question','name'=>'Bu modelin fiyatını hangi unsurlar etkiler?','acceptedAnswer'=>array('@type'=>'Answer','text'=>'Toplam alan, plan, teknik kapsam, kurulum bölgesi, sevkiyat, montaj koşulları ve özel talepler fiyat üzerinde etkili olabilir.')),
        array('@type'=>'Question','name'=>'Prefabrik yapı için zemin hazırlığı gerekir mi?','acceptedAnswer'=>array('@type'=>'Answer','text'=>'Yapı oturum alanı ve zemin hazırlığı proje ve saha koşullarına göre belirlenir. Uygulama öncesinde saha uygunluğunun değerlendirilmesi gerekir.')),
        array('@type'=>'Question','name'=>'Teklif almak için hangi bilgiler gerekli?','acceptedAnswer'=>array('@type'=>'Answer','text'=>'Kullanım amacı, yaklaşık m², tercih edilen oda/bölüm yapısı, kurulum lokasyonu ve iletişim bilgileri ilk değerlendirme için yeterlidir.')),
        array('@type'=>'Question','name'=>'Sevkiyat ve montaj süreci nasıl belirlenir?','acceptedAnswer'=>array('@type'=>'Answer','text'=>'Üretim kapsamı, proje lokasyonu, saha erişimi ve uygulama programına göre sevkiyat ve montaj planı teklif aşamasında netleştirilir.'))
    )
);
?>

<script>
(function(){
  'use strict';

  function q(sel, root){ return (root || document).querySelector(sel); }
  function qa(sel, root){ return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

  /* Gallery thumbnail swapping */
  var mainImg = q('#eg11-main-img');
  qa('.eg11-thumb img').forEach(function(img){
    function removeBrokenThumb(){
      var brokenButton = img.closest ? img.closest('.eg11-thumb') : img.parentNode;
      if (brokenButton && brokenButton.parentNode) brokenButton.parentNode.removeChild(brokenButton);
    }
    img.addEventListener('error', removeBrokenThumb);
    if (img.complete && !img.naturalWidth) removeBrokenThumb();
  });
  qa('.eg11-thumb').forEach(function(btn){
    btn.addEventListener('click', function(){
      qa('.eg11-thumb').forEach(function(b){ b.classList.remove('is-active'); });
      btn.classList.add('is-active');
      if (mainImg) {
        mainImg.src = btn.getAttribute('data-src') || mainImg.src;
        mainImg.setAttribute('data-popup', btn.getAttribute('data-popup') || mainImg.getAttribute('data-popup') || '');
      }
    });
  });

  /* Lightbox */
  var lightbox = q('#eg11-lightbox');
  var lightboxImg = q('#eg11-lightbox-img');
  function openLightbox(){
    if (!mainImg || !lightbox || !lightboxImg) return;
    lightboxImg.src = mainImg.getAttribute('data-popup') || mainImg.src;
    lightbox.classList.add('is-open');
    lightbox.setAttribute('aria-hidden','false');
    document.documentElement.style.overflow='hidden';
  }
  function closeLightbox(){
    if (!lightbox) return;
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden','true');
    document.documentElement.style.overflow='';
  }
  var zoom = q('#eg11-zoom');
  if (zoom) zoom.addEventListener('click', openLightbox);
  if (mainImg) mainImg.addEventListener('dblclick', openLightbox);
  var closeBtn = q('#eg11-lightbox-close');
  if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
  if (lightbox) lightbox.addEventListener('click', function(e){ if (e.target === lightbox) closeLightbox(); });
  document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeLightbox(); });

  /* Smooth section navigation */
  var navLinks = qa('.eg11-nav a[href^="#"], .eg11-btn[href^="#"], .eg11-mobile-bar a[href^="#"]');
  function scrollToHash(hash){
    var target = q(hash);
    if (!target) return false;
    var offset = window.innerWidth <= 640 ? 118 : 145;
    var y = target.getBoundingClientRect().top + window.pageYOffset - offset;
    window.scrollTo({top:y,behavior:'smooth'});
    if (history && history.replaceState) history.replaceState(null,'',hash);
    return true;
  }
  navLinks.forEach(function(link){
    link.addEventListener('click',function(e){
      var hash = link.getAttribute('href');
      if (hash && hash.charAt(0)==='#' && scrollToHash(hash)) e.preventDefault();
    });
  });

  /* Active section indicator */
  var sectionIds = ['eg-plan','eg-highlights','eg-tech','eg-process','eg-faq','eg-lead'];
  function setActive(){
    var marker = window.pageYOffset + 190;
    var active = '';
    sectionIds.forEach(function(id){
      var el = document.getElementById(id);
      if (el && el.offsetTop <= marker) active = '#'+id;
    });
    qa('.eg11-nav a').forEach(function(a){
      var on = a.getAttribute('href') === active;
      a.classList.toggle('is-active',on);
      if(on) a.setAttribute('aria-current','location'); else a.removeAttribute('aria-current');
    });
  }
  var navWrap = q('.eg11-nav-wrap');
  function updateStickyState(){
    if (!navWrap) return;
    var top = navWrap.getBoundingClientRect().top;
    navWrap.classList.toggle('is-stuck', top <= 70);
  }

  window.addEventListener('scroll', function(){
    setActive();
    updateStickyState();
  }, {passive:true});

  window.addEventListener('load', function(){
    setActive();
    updateStickyState();

    /* Only auto-scroll when the current page was intentionally opened with
       a known V11 section hash. Avoid restoring an old #eg-lead state. */
    var allowedHashes = ['#eg-plan','#eg-highlights','#eg-tech','#eg-process','#eg-faq','#eg-lead'];
    if (allowedHashes.indexOf(window.location.hash) !== -1 && q(window.location.hash)) {
      setTimeout(function(){ scrollToHash(window.location.hash); },120);
    }
  });
})();
</script>

<script type="application/ld+json"><?php echo json_encode($eg_product_schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?></script>
<script type="application/ld+json"><?php echo json_encode($eg_breadcrumb_schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?></script>
<script type="application/ld+json"><?php echo json_encode($eg_faq_schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?></script>

<?php echo $footer; ?>
