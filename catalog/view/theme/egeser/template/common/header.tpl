<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?><meta name="description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>" /><?php } ?>
<?php if ($keywords) { ?><meta name="keywords" content="<?php echo htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8'); ?>" /><?php } ?>
<?php if (!empty($egeser_og_title)) { ?><meta property="og:title" content="<?php echo htmlspecialchars($egeser_og_title, ENT_QUOTES, 'UTF-8'); ?>" /><?php } ?>
<?php if (!empty($egeser_og_description)) { ?><meta property="og:description" content="<?php echo htmlspecialchars($egeser_og_description, ENT_QUOTES, 'UTF-8'); ?>" /><?php } ?>
<?php if (!empty($egeser_og_url)) { ?><meta property="og:url" content="<?php echo htmlspecialchars($egeser_og_url, ENT_QUOTES, 'UTF-8'); ?>" /><?php } ?>
<meta property="og:type" content="<?php echo !empty($egeser_og_type) ? htmlspecialchars($egeser_og_type, ENT_QUOTES, 'UTF-8') : 'website'; ?>" />
<meta property="og:site_name" content="Egeser Prefabrik" />
<?php if (!empty($egeser_og_image)) { ?><meta property="og:image" content="<?php echo htmlspecialchars($egeser_og_image, ENT_QUOTES, 'UTF-8'); ?>" /><?php } ?>
<meta name="twitter:card" content="summary" />
<?php if (!empty($egeser_og_title)) { ?><meta name="twitter:title" content="<?php echo htmlspecialchars($egeser_og_title, ENT_QUOTES, 'UTF-8'); ?>" /><?php } ?>
<?php if (!empty($egeser_og_description)) { ?><meta name="twitter:description" content="<?php echo htmlspecialchars($egeser_og_description, ENT_QUOTES, 'UTF-8'); ?>" /><?php } ?>
<?php if (!empty($egeser_og_image)) { ?><meta name="twitter:image" content="<?php echo htmlspecialchars($egeser_og_image, ENT_QUOTES, 'UTF-8'); ?>" /><?php } ?>
<meta name="theme-color" content="#d71920" />
<?php if (!empty($egeser_robots)) { ?><meta name="robots" content="<?php echo htmlspecialchars($egeser_robots, ENT_QUOTES, 'UTF-8'); ?>"><?php } ?>

<?php if (!empty($egeser_tracking_status)) { ?>
  <?php if (!empty($egeser_ga4_id) || !empty($egeser_google_ads_id)) { ?>
  <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
  <link rel="dns-prefetch" href="//www.googletagmanager.com">
  <?php } ?>
  <?php if (!empty($egeser_meta_pixel_id)) { ?>
  <link rel="preconnect" href="https://connect.facebook.net" crossorigin>
  <link rel="dns-prefetch" href="//connect.facebook.net">
  <?php } ?>
<?php } ?>

<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>

<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="catalog/view/theme/default/stylesheet/stylesheet.css" rel="stylesheet">

<?php foreach ($styles as $style) { ?>
<link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<link href="catalog/view/theme/egeser/stylesheet/theme.css" rel="stylesheet" />
<link href="catalog/view/theme/egeser/stylesheet/egeser-image-fit-v1.css" rel="stylesheet" />
<link href="catalog/view/theme/egeser/stylesheet/egeser-ui-audit-v4.css" rel="stylesheet" />

<?php foreach ($scripts as $script) { ?>
<script src="<?php echo $script; ?>"></script>
<?php } ?>
<script src="catalog/view/javascript/common.js" type="text/javascript"></script>
<script src="catalog/view/theme/egeser/javascript/theme.js" defer></script>
<script src="catalog/view/theme/egeser/javascript/egeser-image-fit-v1.js" defer></script>

<?php if (!empty($analytics) && is_array($analytics)) { ?>
<?php foreach ($analytics as $analytic) { echo $analytic; } ?>
<?php } ?>

<?php if (!empty($egeser_tracking_status)) { ?>
<script>
window.EgeserTracking = <?php echo json_encode(array(
  'enabled' => true,
  'ga4' => $egeser_ga4_id,
  'meta' => $egeser_meta_pixel_id,
  'google_ads_id' => $egeser_google_ads_id,
  'google_ads_lead_label' => $egeser_google_ads_lead_label
), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<?php if (!empty($egeser_ga4_id)) { ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo urlencode($egeser_ga4_id); ?>"></script>
<script>
window.dataLayer=window.dataLayer||[];
function gtag(){dataLayer.push(arguments);}
gtag('js',new Date());
gtag('config','<?php echo htmlspecialchars($egeser_ga4_id, ENT_QUOTES, 'UTF-8'); ?>',{anonymize_ip:true});
<?php if (!empty($egeser_google_ads_id)) { ?>gtag('config','<?php echo htmlspecialchars($egeser_google_ads_id, ENT_QUOTES, 'UTF-8'); ?>');<?php } ?>
</script>
<?php } ?>
<?php if (!empty($egeser_meta_pixel_id)) { ?>
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}
(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init','<?php echo htmlspecialchars($egeser_meta_pixel_id, ENT_QUOTES, 'UTF-8'); ?>');
fbq('track','PageView');
</script>
<?php } ?>
<?php } ?>

<style id="egeser-shell-v17-2-3-mega-menu-layer-fix">
:root{
  --egs-red:#F21B22;--egs-red2:#D9151B;--egs-ink:#121212;--egs-dark:#1d1d1d;
  --egs-text:#2f3032;--egs-muted:#747477;--egs-line:#E8E3DD;--egs-soft:#F7F5F2;--egs-white:#fff;
  --egs-orange:#F4A126;--egs-orange-soft:#FFF6EA;
}
body{padding-top:0!important}
.eg-shell-container{width:min(calc(100% - 42px),1240px);margin:0 auto}

/* TOP UTILITY BAR */
.eg112-top{background:#171717;color:#fff;font-size:12px}
.eg112-top__inner{min-height:38px;display:flex;align-items:center;justify-content:space-between;gap:24px}
.eg112-top__left,.eg112-top__right{display:flex;align-items:center;gap:18px}
.eg112-top a,.eg112-top span{color:#e8e8e8;text-decoration:none}
.eg112-top a:hover{color:#fff}
.eg112-top__item{display:inline-flex;align-items:center;gap:7px;white-space:nowrap}
.eg112-top svg{width:14px;height:14px;opacity:.86}
.eg112-top__pill{padding:6px 11px;border:1px solid #3a3a3a;border-radius:999px;background:#222;font-size:11px;font-weight:700;letter-spacing:.01em}

/* HEADER */
.eg112-header{position:relative;z-index:1000;background:rgba(255,255,255,.98);border-bottom:1px solid var(--egs-line)}
.eg112-header.is-sticky{position:sticky;top:0;box-shadow:0 12px 30px rgba(0,0,0,.06)}
.eg112-header__inner{min-height:86px;display:grid;grid-template-columns:275px minmax(0,1fr) auto;align-items:center;gap:24px}
.eg112-brand{display:flex;align-items:center;min-width:0}
.eg112-brand a{display:inline-flex;align-items:center;gap:12px;text-decoration:none}
.eg112-brand img{display:block;width:225px;height:auto;max-width:100%;max-height:70px;object-fit:contain;transform:scale(1.12);transform-origin:left center}
.eg112-brand__mark{width:42px;height:42px;display:grid;place-items:center;border-radius:12px;background:var(--egs-red);color:#fff;font-weight:900}
.eg112-brand__text{display:flex;flex-direction:column;line-height:1}
.eg112-brand__text strong{font-size:22px;letter-spacing:.02em;color:#171717}
.eg112-brand__text span{margin-top:5px;font-size:10px;font-weight:800;letter-spacing:.18em;color:var(--egs-red);text-transform:uppercase}

.eg112-nav{display:flex;align-items:center;justify-content:center;gap:3px;flex-wrap:nowrap;white-space:nowrap}
.eg112-nav>a,.eg112-nav__toggle{display:inline-flex;align-items:center;gap:6px;padding:12px 11px;border:0;border-radius:9px;background:transparent;color:#262626;font-size:13px;font-weight:800;text-decoration:none;cursor:pointer;white-space:nowrap}
.eg112-nav>a:hover,.eg112-nav__toggle:hover{background:#f3f3f0;color:#000}
.eg112-nav__togglewrap{display:flex;align-items:center;border-radius:9px}
.eg112-nav__mainlink{display:inline-flex;align-items:center;padding:12px 7px 12px 11px;border-radius:9px 0 0 9px;color:#262626;font-size:13px;font-weight:800;text-decoration:none;white-space:nowrap}
.eg112-nav__mainlink:hover{background:#f3f3f0;color:#000;text-decoration:none}
.eg112-nav__toggle{padding:12px 8px 12px 5px;border-radius:0 9px 9px 0}
.eg112-nav__toggle svg{width:12px;height:12px;transition:transform .2s ease}
.eg112-mega.is-open .eg112-nav__toggle svg{transform:rotate(180deg)}

.eg112-actions{display:flex;align-items:center;gap:9px}
.eg112-actions__phone{width:44px;height:44px;display:grid;place-items:center;border:1px solid var(--egs-line);border-radius:11px;color:#222;background:#fff;text-decoration:none}
.eg112-actions__phone svg{width:18px;height:18px}
.eg112-actions__quote{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:0 18px;border-radius:11px;background:var(--egs-red);color:#fff;font-size:13px;font-weight:900;text-decoration:none}
.eg112-actions__quote:hover{background:var(--egs-red2);color:#fff}

/* MEGA MENU — V19 */
.eg112-mega{position:relative}
.eg112-mega-panel{
  position:absolute;right:0;top:calc(100% + 16px);
  width:840px;max-width:calc(100vw - 60px);
  padding:20px;
  border:1px solid var(--egs-line);
  border-radius:20px;
  background:#fff;
  box-shadow:0 30px 70px rgba(18,18,18,.16);
  opacity:0;visibility:hidden;pointer-events:none;
  transform:translateY(-8px);
  transition:opacity .16s ease,transform .16s ease,visibility .16s;
}
.eg112-mega.is-open .eg112-mega-panel{opacity:1;visibility:visible;pointer-events:auto;transform:translateY(0)}
.eg112-mega-panel:before{content:"";position:absolute;top:-9px;right:36px;width:16px;height:16px;background:#fff;border-left:1px solid var(--egs-line);border-top:1px solid var(--egs-line);transform:rotate(45deg)}

.eg112-mega-grid{display:flex;gap:18px;align-items:stretch}

.eg112-mega-intro{
  flex:0 0 214px;
  min-width:0;
  display:flex;flex-direction:column;
  padding:20px;
  border-radius:16px;
  color:#fff;
  background:
    radial-gradient(circle at 100% 100%,rgba(244,161,38,.16),transparent 32%),
    radial-gradient(circle at 0% 0%,rgba(242,27,34,.12),transparent 28%),
    #171717;
}
.eg112-mega-intro>*{min-width:0}
.eg112-mega-intro small{display:block;color:var(--egs-orange);font-size:10px;font-weight:900;letter-spacing:.14em;text-transform:uppercase}
.eg112-mega-intro strong{display:block;margin-top:9px;font-size:19px;line-height:1.22}
.eg112-mega-intro p{margin:9px 0 16px;color:#c7c7c7;font-size:11.5px;line-height:1.6}
.eg112-mega-cta{display:inline-flex;align-items:center;gap:6px;width:fit-content;padding:9px 12px;border-radius:9px;background:var(--egs-red);color:#fff!important;font-size:12px;font-weight:900;text-decoration:none!important}
.eg112-mega-cta:hover{background:var(--egs-red2)}
.eg112-mega-intro-trust{margin-top:auto;padding-top:14px;border-top:1px solid rgba(255,255,255,.14);display:flex;flex-direction:column;gap:7px;min-width:0}
.eg112-mega-intro-trust span{display:flex;align-items:center;gap:7px;font-size:11px;color:#dcdcdc;font-weight:600;min-width:0}
.eg112-mega-intro-trust svg{flex:0 0 auto;width:13px;height:13px;color:var(--egs-orange)}

.eg112-mega-col{flex:1 1 0;min-width:0;padding:2px}
.eg112-mega-col-head{display:flex;align-items:center;gap:8px;min-width:0;margin:0 0 8px;padding:0 8px}
.eg112-mega-col-icon{width:26px;height:26px;flex:0 0 auto;display:flex;align-items:center;justify-content:center;border-radius:8px;background:var(--egs-orange-soft);color:var(--egs-red)}
.eg112-mega-col-icon svg{width:14px;height:14px}
.eg112-mega-col-head h3{margin:0;color:#171717;font-size:12px;font-weight:900;letter-spacing:.03em}
.eg112-mega-col a{display:flex;align-items:flex-start;gap:10px;min-width:0;padding:10px 8px;border-radius:10px;color:#252525;text-decoration:none;transition:background .15s ease,transform .15s ease}
.eg112-mega-col a:hover{background:var(--egs-orange-soft);transform:translateX(2px)}
.eg112-mega-col a .eg112-mega-dot{flex:0 0 auto;margin-top:6px;width:6px;height:6px;border-radius:50%;background:var(--egs-line);transition:background .15s ease}
.eg112-mega-col a:hover .eg112-mega-dot{background:var(--egs-red)}
.eg112-mega-col a>div{min-width:0}
.eg112-mega-col a strong{display:block;font-size:13px;line-height:1.35}
.eg112-mega-col a:hover strong{color:var(--egs-red)}
.eg112-mega-col a span{display:block;margin-top:3px;color:#858585;font-size:10.5px;line-height:1.5}

/* MOBILE MENU */
.eg112-toggle{display:none;width:44px;height:44px;border:1px solid var(--egs-line);border-radius:11px;background:#fff;padding:0;cursor:pointer}
.eg112-toggle span{display:block;width:19px;height:2px;margin:4px auto;background:#222;border-radius:2px}
.eg112-mobile-panel{display:none}

/* FOOTER */
.eg112-footer{margin-top:80px;background:#171717;color:#fff}
.eg112-footer__top{padding:58px 0 44px}
.eg112-footer__grid{display:grid;grid-template-columns:1.3fr .85fr .85fr 1fr;gap:44px}
.eg112-footer__brand{margin-bottom:15px;font-size:27px;font-weight:900;letter-spacing:.01em}
.eg112-footer__brand span{color:var(--egs-red)}
.eg112-footer__about{max-width:390px;margin:0;color:#aaa;line-height:1.75}
.eg112-footer__contact{display:grid;gap:8px;margin-top:20px}
.eg112-footer__contact a,.eg112-footer__contact span{color:#dedede;text-decoration:none;font-size:13px}
.eg112-footer h4{margin:4px 0 14px;color:#fff;font-size:12px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}
.eg112-footer__links{display:grid;gap:9px}
.eg112-footer__links a{color:#aaa;text-decoration:none;font-size:13px}
.eg112-footer__links a:hover{color:#fff}
.eg112-footer__social{display:flex;flex-wrap:wrap;gap:7px;margin-top:18px}
.eg112-footer__social a{display:inline-flex;padding:7px 9px;border:1px solid #333;border-radius:8px;color:#ddd;text-decoration:none;font-size:11px}
.eg112-footer__cta{margin-top:18px;padding:16px;border:1px solid #303030;border-radius:14px;background:#202020}
.eg112-footer__cta strong{display:block;font-size:14px}
.eg112-footer__cta span{display:block;margin:4px 0 11px;color:#aaa;font-size:11px}
.eg112-footer__cta a{display:inline-flex;padding:9px 12px;border-radius:9px;background:var(--egs-red);color:#fff;text-decoration:none;font-size:12px;font-weight:900}
.eg112-footer__bottom{border-top:1px solid #2b2b2b}
.eg112-footer__bottom-inner{min-height:58px;display:flex;align-items:center;justify-content:space-between;gap:20px;color:#858585;font-size:11px}
.eg112-footer__legal{display:flex;gap:14px;flex-wrap:wrap}
.eg112-footer__legal a{color:#858585;text-decoration:none}

/* MOBILE FIXED BAR */
.eg112-mobilebar{display:none}

@media(max-width:1080px){
 .eg112-header__inner{grid-template-columns:245px 1fr auto}
 .eg112-nav>a,.eg112-nav__toggle{padding:10px 8px;font-size:12px}
}
@media(max-width:920px){
 .eg112-top__right{display:none}
 .eg112-header__inner{grid-template-columns:1fr auto auto;min-height:72px}
 .eg112-nav{display:none}
 .eg112-toggle{display:block}
 .eg112-actions__phone{display:none}
 .eg112-mobile-panel{display:none;border-top:1px solid var(--egs-line);background:#fff}
 .eg112-mobile-panel.is-open{display:block}
 .eg112-mobile-panel__inner{padding:12px 0 18px}
 .eg112-mobile-panel a,.eg112-mobile-panel button{display:flex;width:100%;align-items:center;justify-content:space-between;padding:12px 4px;border:0;border-bottom:1px solid #f0f0ed;background:#fff;color:#252525;text-align:left;text-decoration:none;font-weight:800}
 .eg112-mobile-sub{display:none;padding:5px 0 7px 14px}
 .eg112-mobile-sub.is-open{display:block}
 .eg112-mobile-sub a{font-weight:600;color:#555}
 .eg112-footer__grid{grid-template-columns:1fr 1fr;gap:34px}
}
@media(max-width:640px){
 .eg-shell-container{width:min(calc(100% - 28px),1240px)}
 .eg112-top__inner{min-height:34px}
 .eg112-top__left{gap:12px}
 .eg112-top__left .eg112-location{display:none}
 .eg112-brand__text strong{font-size:19px}
 .eg112-brand__mark{width:38px;height:38px}
 .eg112-actions__quote{padding:0 13px;font-size:12px}
 .eg112-footer{margin-top:58px}
 .eg112-footer__grid{grid-template-columns:1fr}
 .eg112-footer__bottom{padding-bottom:64px}
 .eg112-footer__bottom-inner{align-items:flex-start;flex-direction:column;justify-content:center;padding:14px 0}
 .eg112-mobilebar{position:fixed;left:0;right:0;bottom:0;z-index:9997;display:grid;grid-template-columns:1fr 1fr 1.2fr;gap:1px;padding:7px;background:#161616;box-shadow:0 -8px 30px rgba(0,0,0,.18)}
 .eg112-mobilebar a{display:flex;align-items:center;justify-content:center;min-height:48px;border-radius:8px;color:#fff;text-decoration:none;font-size:12px;font-weight:900}
 .eg112-mobilebar .wa{background:#176f64}
 .eg112-mobilebar .quote{background:var(--egs-red)}
}

/* =========================================================
   V16 PREMIUM HEADER — EGESER 3-COLOR SYSTEM
   (renk degiskenleri yukaridaki tek :root bloguna tasindi)
   ========================================================= */
.eg112-top{
  background:linear-gradient(90deg,#111,#171717 55%,#1d1711);
  border-bottom:1px solid rgba(244,161,38,.10);
}
.eg112-top__item svg{color:var(--egs-orange)}
.eg112-top__pill{
  border-color:rgba(244,161,38,.24);
  background:rgba(244,161,38,.07);
}
.eg112-top__right>a:hover{color:var(--egs-orange)}

.eg112-header{
  background:#fff;
}
.eg112-header.is-sticky{
  box-shadow:0 14px 34px rgba(18,18,18,.08);
  border-bottom-color:rgba(244,161,38,.20);
}

.eg112-nav>a,.eg112-nav__mainlink{
  position:relative;
}
.eg112-nav>a:after,.eg112-nav__mainlink:after{
  content:"";
  position:absolute;left:11px;right:11px;bottom:5px;
  height:2px;border-radius:2px;
  background:linear-gradient(90deg,var(--egs-red),var(--egs-orange));
  transform:scaleX(0);
  transform-origin:left center;
  transition:transform .2s ease;
}
.eg112-nav>a:hover,.eg112-nav__mainlink:hover{
  background:var(--egs-orange-soft);
  color:#111;
}
.eg112-nav>a:hover:after,.eg112-nav__mainlink:hover:after,
.eg112-nav>a.is-active:after,.eg112-nav__mainlink.is-active:after{
  transform:scaleX(1);
}
.eg112-nav>a.is-active,.eg112-nav__mainlink.is-active{
  background:#fff8ef;
  color:#111;
}
.eg112-nav>a.is-active:before,.eg112-nav__mainlink.is-active:before{
  content:"";
  width:5px;height:5px;margin-right:2px;border-radius:50%;
  background:var(--egs-orange);
}

.eg112-actions__phone{
  border-color:var(--egs-line);
  transition:.2s ease;
}
.eg112-actions__phone:hover{
  color:var(--egs-red);
  border-color:var(--egs-orange);
  background:var(--egs-orange-soft);
}
.eg112-actions__quote{
  position:relative;
  overflow:hidden;
  background:var(--egs-red);
  box-shadow:0 10px 24px rgba(242,27,34,.14);
}
.eg112-actions__quote:after{
  content:"";
  position:absolute;top:0;bottom:0;left:-40%;
  width:30%;
  transform:skewX(-22deg);
  background:rgba(255,255,255,.18);
  transition:left .35s ease;
}
.eg112-actions__quote:hover:after{left:115%}
.eg112-actions__quote:hover{background:var(--egs-red2)}

.eg112-toggle:hover{border-color:var(--egs-orange);background:var(--egs-orange-soft)}
.eg112-toggle span{transition:.2s ease}
.eg112-toggle[aria-expanded="true"] span{background:var(--egs-red)}

@media(max-width:920px){
  .eg112-mobile-panel{
    box-shadow:0 20px 36px rgba(18,18,18,.09);
  }
  .eg112-mobile-panel a,.eg112-mobile-panel button{
    padding:13px 2px;
  }
  .eg112-mobile-panel a:hover,.eg112-mobile-panel button:hover{
    color:var(--egs-red);
    background:var(--egs-orange-soft);
  }
  .eg112-mobile-sub{
    margin:0 -4px;
    padding:6px 10px 8px 14px;
    background:#faf7f2;
    border-left:3px solid var(--egs-orange);
  }
}
@media(max-width:480px){
  .eg112-header__inner{
    min-height:68px;
    grid-template-columns:minmax(0,1fr) auto;
    gap:10px;
  }
  .eg112-brand img{
    width:158px;
    max-height:56px;
    transform:scale(1.03);
  }
  .eg112-actions{gap:6px}
  .eg112-actions__quote{display:none}
  .eg112-toggle{width:42px;height:42px}
  .eg112-mobile-panel__inner{padding-bottom:14px}
}
@media(max-width:390px){
  .eg-shell-container{width:min(calc(100% - 24px),1240px)}
  .eg112-top{font-size:11px}
  .eg112-top__inner{min-height:32px}
  .eg112-brand img{width:148px}
  .eg112-mobilebar{padding:6px}
  .eg112-mobilebar a{min-height:46px;font-size:11px}
}


/* =========================================================
   V17 FLOATING WHATSAPP
   V18 FLOATING CONTACT STACK (WhatsApp + Telefon + E-posta)
   ========================================================= */
.eg17-float-stack{
  position:fixed;
  right:24px;
  bottom:24px;
  z-index:9995;
  display:flex;
  flex-direction:column;
  align-items:flex-end;
  gap:14px;
}
.eg17-float-wa{
  display:flex;
  align-items:center;
  gap:9px;
  text-decoration:none!important;
  color:#fff!important;
  filter:drop-shadow(0 12px 24px rgba(18,18,18,.18));
}
.eg17-float-wa__label{
  display:block;
  padding:10px 13px;
  border-radius:12px;
  background:#151515;
  border:1px solid rgba(244,161,38,.18);
  font-size:11px;
  font-weight:900;
  white-space:nowrap;
  opacity:0;
  transform:translateX(10px);
  pointer-events:none;
  transition:.2s ease;
}
.eg17-float-wa__icon{
  width:56px;height:56px;
  display:flex;align-items:center;justify-content:center;
  border-radius:50%;
  background:#25D366;
  border:3px solid #fff;
  box-shadow:0 0 0 1px rgba(18,18,18,.06);
  transition:transform .2s ease,box-shadow .2s ease;
}
.eg17-float-wa--call .eg17-float-wa__icon{background:var(--egs-red)}
.eg17-float-wa--mail .eg17-float-wa__icon{background:var(--egs-orange)}
.eg17-float-wa__icon svg{width:28px;height:28px;fill:#fff}
.eg17-float-wa--call .eg17-float-wa__icon svg,
.eg17-float-wa--mail .eg17-float-wa__icon svg{fill:none;stroke:#fff}
.eg17-float-wa:hover .eg17-float-wa__label{
  opacity:1;
  transform:translateX(0);
}
.eg17-float-wa:hover .eg17-float-wa__icon{
  transform:translateY(-2px) scale(1.04);
  box-shadow:0 12px 28px rgba(37,211,102,.28);
}
.eg17-float-wa--call:hover .eg17-float-wa__icon{box-shadow:0 12px 28px rgba(242,27,34,.28)}
.eg17-float-wa--mail:hover .eg17-float-wa__icon{box-shadow:0 12px 28px rgba(244,161,38,.28)}

/* Sol alt: "Bize Ulaşın" */
.eg17-float-contact{
  position:fixed;
  left:24px;
  bottom:24px;
  z-index:9995;
  display:flex;
  align-items:center;
  gap:9px;
  padding:12px 18px 12px 12px;
  border-radius:999px;
  background:linear-gradient(90deg,var(--egs-red),var(--egs-orange));
  color:#fff!important;
  text-decoration:none!important;
  font-size:13px;
  font-weight:900;
  white-space:nowrap;
  filter:drop-shadow(0 12px 24px rgba(18,18,18,.18));
  transition:transform .2s ease,box-shadow .2s ease;
}
.eg17-float-contact:hover{
  transform:translateY(-2px);
  box-shadow:0 12px 28px rgba(242,27,34,.28);
}
.eg17-float-contact__icon{
  width:32px;height:32px;
  flex:0 0 auto;
  display:flex;align-items:center;justify-content:center;
  border-radius:50%;
  background:rgba(255,255,255,.22);
}
.eg17-float-contact__icon svg{width:18px;height:18px}

@media(max-width:920px){
  .eg17-float-stack{
    right:14px;
    bottom:76px;
  }
  .eg17-float-wa__label{display:none}
  .eg17-float-wa__icon{width:52px;height:52px}
  .eg17-float-contact{
    left:14px;
    bottom:76px;
    width:52px;height:52px;
    padding:0;
    justify-content:center;
    border-radius:50%;
  }
  .eg17-float-contact__label{display:none}
  .eg17-float-contact__icon{width:100%;height:100%;background:transparent}
}
@media(max-width:390px){
  .eg17-float-stack{right:11px;bottom:72px}
  .eg17-float-wa__icon{width:49px;height:49px}
  .eg17-float-wa__icon svg{width:25px;height:25px}
  .eg17-float-contact{left:11px;bottom:72px;width:49px;height:49px}
}


/* =========================================================
   V17.2 FINAL FIX — FOOTER LOGO VISIBILITY
   ========================================================= */
footer .eg17-footer-logo-plate{
  display:inline-flex!important;
  align-items:center;
  justify-content:center;
  padding:10px 12px!important;
  margin-bottom:14px;
  border-radius:14px;
  background:#fff!important;
  border:1px solid rgba(244,161,38,.22);
  box-shadow:0 10px 28px rgba(0,0,0,.16);
}
footer .eg17-footer-logo-img{
  display:block!important;
  width:auto!important;
  max-width:128px!important;
  max-height:72px!important;
  margin:0!important;
  opacity:1!important;
  filter:none!important;
}
@media(max-width:600px){
  footer .eg17-footer-logo-plate{
    padding:8px 10px!important;
  }
  footer .eg17-footer-logo-img{
    max-width:116px!important;
  }
}


/* =========================================================
   V17.2.1 FOOTER LOGO HARD FIX
   Direct selector: no JS dependency
   ========================================================= */
.eg112-footer img{
  display:block!important;
  width:auto!important;
  height:auto!important;
  max-width:128px!important;
  max-height:78px!important;
  box-sizing:content-box!important;
  padding:10px 14px!important;
  margin:0 0 16px 0!important;
  border:1px solid rgba(244,161,38,.26)!important;
  border-radius:14px!important;
  background:#ffffff!important;
  opacity:1!important;
  visibility:visible!important;
  filter:none!important;
  box-shadow:0 10px 26px rgba(0,0,0,.18)!important;
}
.eg112-footer img:hover{
  background:#ffffff!important;
  opacity:1!important;
}
@media(max-width:640px){
  .eg112-footer img{
    max-width:116px!important;
    max-height:70px!important;
    padding:9px 12px!important;
  }
}



/* =========================================================
   V17.3 MOBILE HEADER HARD FIX
   ========================================================= */
html,body{
  max-width:100%!important;
  overflow-x:hidden!important;
}
.eg112-top,
.eg112-header,
.eg112-mobile-panel{
  width:100%!important;
  max-width:100%!important;
  overflow-x:hidden!important;
}

@media(max-width:600px){
  .eg-shell-container{
    width:auto!important;
    max-width:none!important;
    margin-left:14px!important;
    margin-right:14px!important;
  }

  .eg112-top__inner{
    min-width:0!important;
    width:100%!important;
  }
  .eg112-top__left{
    min-width:0!important;
    max-width:100%!important;
  }
  .eg112-top__item{
    min-width:0!important;
  }

  .eg112-header__inner{
    display:flex!important;
    align-items:center!important;
    justify-content:space-between!important;
    width:auto!important;
    max-width:none!important;
    min-width:0!important;
    min-height:68px!important;
    gap:10px!important;
  }
  .eg112-brand{
    flex:1 1 auto!important;
    min-width:0!important;
    max-width:calc(100% - 54px)!important;
    overflow:hidden!important;
  }
  .eg112-brand a{
    width:100%!important;
    max-width:100%!important;
    min-width:0!important;
  }
  .eg112-brand img{
    width:122px!important;
    max-width:122px!important;
    max-height:54px!important;
    transform:none!important;
    object-fit:contain!important;
    object-position:left center!important;
  }
  .eg112-actions{
    flex:0 0 auto!important;
    min-width:42px!important;
    display:flex!important;
    justify-content:flex-end!important;
  }
  .eg112-actions__phone,
  .eg112-actions__quote{
    display:none!important;
  }
  .eg112-toggle{
    display:block!important;
    flex:0 0 42px!important;
    width:42px!important;
    height:42px!important;
    margin:0!important;
  }
  .eg112-mobile-panel{
    position:relative!important;
    left:auto!important;
    right:auto!important;
    width:100%!important;
  }
  .eg112-mobile-panel__inner{
    width:auto!important;
    margin-left:14px!important;
    margin-right:14px!important;
  }

  /* Floating WhatsApp stays above fixed mobile CTA */
  .eg17-float-stack{
    right:12px!important;
    bottom:72px!important;
  }
  .eg17-float-contact{
    left:12px!important;
    bottom:72px!important;
  }
}

@media(max-width:390px){
  .eg112-brand img{
    width:116px!important;
    max-width:116px!important;
  }
}


/* =========================================================
   V17.3.1 MOBILE POLISH SUPPORT
   ========================================================= */
@media(max-width:600px){
  .eg17-float-stack{
    right:14px!important;
    bottom:86px!important;
  }
  .eg17-float-contact{
    left:14px!important;
    bottom:86px!important;
  }
}


/* =========================================================
   V17.3.2 MOBILE CTA / FOOTER ALIGN FIX SUPPORT
   ========================================================= */
@media(max-width:600px){
  .eg112-footer__grid > :first-child{
    text-align:center!important;
  }
  .eg112-footer__grid > :first-child .eg112-footer__about{
    margin-left:auto!important;
    margin-right:auto!important;
  }
  .eg112-footer__grid > :first-child .eg112-footer__contact{
    justify-items:center!important;
  }
  .eg112-footer img{
    margin-left:auto!important;
    margin-right:auto!important;
  }
}


/* =========================================================
   V17.3.4 ACTUAL MOBILE BAR FIX
   ========================================================= */
@media(max-width:600px){
  body{padding-bottom:52px!important}
  .eg17-float-stack{
    right:12px!important;
    bottom:64px!important;
  }
  .eg17-float-contact{
    left:12px!important;
    bottom:64px!important;
  }
}
@media(max-width:390px){
  body{padding-bottom:48px!important}
}


/* =========================================================
   V17.3.5 MOBILE WIDTH LOCK SUPPORT
   ========================================================= */
@media(max-width:600px){
  .eg-shell-container,
  .eg112-header__inner,
  .eg112-top__inner{
    box-sizing:border-box!important;
  }
}




/* =========================================================
   V17.2.3 DESKTOP MEGA MENU LAYER FIX
   Prevent mega menu from rendering behind page/category UI.
   Mobile behavior is not changed.
   ========================================================= */
@media(min-width:921px){
  .eg112-header{
    position:relative!important;
    z-index:99990!important;
    overflow:visible!important;
    isolation:isolate!important;
  }
  .eg112-header__inner,
  .eg112-nav,
  .eg112-mega,
  .eg112-nav__togglewrap{
    overflow:visible!important;
  }
  .eg112-nav{
    position:relative!important;
    z-index:99991!important;
  }
  .eg112-mega{
    position:relative!important;
    z-index:99992!important;
  }
  .eg112-mega-panel{
    z-index:99999!important;
  }
  .eg112-mega.is-open{
    z-index:100000!important;
  }
  .eg112-mega.is-open .eg112-mega-panel{
    display:block!important;
    opacity:1!important;
    visibility:visible!important;
    pointer-events:auto!important;
  }
}

</style>

</head>
<body class="<?php echo $class; ?><?php echo !empty($egeser_performance_content_visibility) ? ' eg-perf-content-visibility' : ''; ?>">
<?php
$eg112_phone = !empty($telephone) ? trim($telephone) : '';
if ($eg112_phone === '' || preg_replace('/\D+/', '', $eg112_phone) === '123456789') {
    $eg112_phone = '0531 886 60 90';
}
$eg112_phone_href = 'tel:' . preg_replace('/[^0-9+]/', '', $eg112_phone);
$eg112_contact = '/iletisim';
$eg112_prefabrik = !empty($egeser_url_prefabrik_yapilar) ? $egeser_url_prefabrik_yapilar : '/prefabrik-yapilar';
$eg112_projeler = !empty($egeser_url_referanslar) ? $egeser_url_referanslar : '/projelerimiz';
$eg112_teknik = !empty($egeser_url_teknik) ? $egeser_url_teknik : '/teknik-bilgiler';
$eg112_kurumsal = '/hakkimizda';
$eg112_blog = !empty($egeser_url_blog) ? $egeser_url_blog : '/blog';
$eg112_logo_ok = !empty($logo) && stripos($logo, 'catalog/view/theme/default/image/logo.png') === false;
?>

<div class="eg112-top">
  <div class="eg-shell-container eg112-top__inner">
    <div class="eg112-top__left">
      <a class="eg112-top__item" href="<?php echo $eg112_phone_href; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.8 2.1z"></path></svg>
        <?php echo htmlspecialchars($eg112_phone, ENT_QUOTES, 'UTF-8'); ?>
      </a>
      <span class="eg112-top__item eg112-location">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>
        İzmir / Kemalpaşa
      </span>
    </div>
    <div class="eg112-top__right">
      <span class="eg112-top__pill">Bireysel & Kurumsal Prefabrik Çözümler</span>
      <a href="<?php echo $eg112_contact; ?>">Showroom</a>
      <?php if (!empty($egeser_url_e_katalog)) { ?><a href="<?php echo htmlspecialchars($egeser_url_e_katalog, ENT_QUOTES, 'UTF-8'); ?>" data-eg-track="brochure_click" data-placement="header">E-Katalog</a><?php } ?>
    </div>
  </div>
</div>

<header class="eg112-header" id="eg112-header">
  <div class="eg-shell-container eg112-header__inner">
    <div class="eg112-brand">
      <a href="/" aria-label="Egeser Prefabrik Ana Sayfa">
        <?php if ($eg112_logo_ok) { ?>
          <img src="<?php echo htmlspecialchars($logo, ENT_QUOTES, 'UTF-8'); ?>" alt="Egeser Prefabrik" width="196" height="58">
        <?php } else { ?>
          <span class="eg112-brand__mark">E</span>
          <span class="eg112-brand__text"><strong>EGESER</strong><span>PREFABRİK</span></span>
        <?php } ?>
      </a>
    </div>

    <nav class="eg112-nav" aria-label="Ana menü">
      <a data-eg-nav="home" href="/">Ana Sayfa</a>

      <div class="eg112-mega" id="eg112-mega">
        <div class="eg112-nav__togglewrap">
          <a class="eg112-nav__mainlink" data-eg-nav="prefabrik" href="<?php echo htmlspecialchars($eg112_prefabrik, ENT_QUOTES, 'UTF-8'); ?>">Prefabrik Yapılar</a>
          <button class="eg112-nav__toggle" id="eg112-mega-toggle" type="button" aria-expanded="false" aria-label="Prefabrik Yapılar alt menüsünü aç">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"></path></svg>
          </button>
        </div>

        <div class="eg112-mega-panel" role="region" aria-label="Prefabrik Yapılar Menüsü">
          <div class="eg112-mega-grid">
            <div class="eg112-mega-intro">
              <small>PREFABRİK YAPILAR</small>
              <strong>İhtiyacınıza uygun yapıyı birlikte planlayalım.</strong>
              <p>Bireysel yaşam alanlarından kurumsal prefabrik projelere kadar tüm çözümleri tek çatı altında inceleyin.</p>
              <a class="eg112-mega-cta" href="/prefabrik-ev-modelleri">Tüm Modeller <span aria-hidden="true">→</span></a>
              <div class="eg112-mega-intro-trust">
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>Kendi Üretim Tesisimiz</span>
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>Profesyonel Montaj</span>
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>Satış Sonrası İletişim</span>
              </div>
            </div>

            <div class="eg112-mega-col">
              <div class="eg112-mega-col-head">
                <span class="eg112-mega-col-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 9-8 9 8"></path><path d="M5 10v10h14V10"></path></svg></span>
                <h3>Bireysel</h3>
              </div>
              <a href="<?php echo !empty($egeser_url_tek_katli) ? $egeser_url_tek_katli : '/tek-katli-prefabrik-evler'; ?>"><span class="eg112-mega-dot"></span><div><strong>Tek Katlı Prefabrik Evler</strong><span>Fonksiyonel tek katlı yaşam modelleri</span></div></a>
              <a href="<?php echo !empty($egeser_url_cift_katli) ? $egeser_url_cift_katli : '/cift-katli-prefabrik-evler'; ?>"><span class="eg112-mega-dot"></span><div><strong>Çift Katlı Prefabrik Evler</strong><span>Dubleks ve geniş aile çözümleri</span></div></a>
              <a href="/prefabrik-ev-modelleri"><span class="eg112-mega-dot"></span><div><strong>Tüm Prefabrik Ev Modelleri</strong><span>Tüm bireysel model seçenekleri</span></div></a>
            </div>

            <div class="eg112-mega-col">
              <div class="eg112-mega-col-head">
                <span class="eg112-mega-col-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"></path><path d="M5 21V7l7-4 7 4v14"></path><path d="M9 21v-6h6v6"></path></svg></span>
                <h3>Kurumsal</h3>
              </div>
              <a href="<?php echo !empty($egeser_url_ofis_yonetim) ? $egeser_url_ofis_yonetim : '/prefabrik-ofis-ve-yonetim-binalari'; ?>"><span class="eg112-mega-dot"></span><div><strong>Ofis & Yönetim Binaları</strong><span>İdari ve ticari kullanım</span></div></a>
              <a href="<?php echo !empty($egeser_url_yatakhane) ? $egeser_url_yatakhane : '/prefabrik-yatakhane-binalari'; ?>"><span class="eg112-mega-dot"></span><div><strong>Yatakhane Binaları</strong><span>Personel konaklama çözümleri</span></div></a>
              <a href="<?php echo !empty($egeser_url_yemekhane) ? $egeser_url_yemekhane : '/prefabrik-yemekhane-binalari'; ?>"><span class="eg112-mega-dot"></span><div><strong>Yemekhane Binaları</strong><span>Toplu kullanım alanları</span></div></a>
              <a href="<?php echo !empty($egeser_url_santiye) ? $egeser_url_santiye : '/prefabrik-santiye-yapilari'; ?>"><span class="eg112-mega-dot"></span><div><strong>Şantiye Yapıları</strong><span>Saha ve proje yapıları</span></div></a>
              <a href="<?php echo !empty($egeser_url_sosyal_tesis) ? $egeser_url_sosyal_tesis : '/prefabrik-sosyal-tesis-yapilari'; ?>"><span class="eg112-mega-dot"></span><div><strong>Sosyal Tesis Yapıları</strong><span>Çok amaçlı kurumsal alanlar</span></div></a>
              <a href="<?php echo !empty($egeser_url_ozel_proje) ? $egeser_url_ozel_proje : '/ozel-proje-prefabrik-yapilar'; ?>"><span class="eg112-mega-dot"></span><div><strong>Özel Proje Prefabrik Yapılar</strong><span>İhtiyaca göre özel projelendirme</span></div></a>
            </div>
          </div>
        </div>
      </div>

      <a data-eg-nav="projeler" href="<?php echo htmlspecialchars($eg112_projeler, ENT_QUOTES, 'UTF-8'); ?>">Projelerimiz</a>
      <a data-eg-nav="teknik" href="<?php echo htmlspecialchars($eg112_teknik, ENT_QUOTES, 'UTF-8'); ?>">Teknik Bilgiler</a>
      <a data-eg-nav="kurumsal" href="<?php echo htmlspecialchars($eg112_kurumsal, ENT_QUOTES, 'UTF-8'); ?>">Hakkımızda</a>
      <a data-eg-nav="blog" href="<?php echo htmlspecialchars($eg112_blog, ENT_QUOTES, 'UTF-8'); ?>">Blog</a>
      <a data-eg-nav="iletisim" href="<?php echo htmlspecialchars($eg112_contact, ENT_QUOTES, 'UTF-8'); ?>">İletişim</a>
    </nav>

    <div class="eg112-actions">
      <a class="eg112-actions__phone" href="<?php echo $eg112_phone_href; ?>" aria-label="Telefon ile ara">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7"></path></svg>
      </a>
      <a class="eg112-actions__quote" href="<?php echo htmlspecialchars($eg112_contact, ENT_QUOTES, 'UTF-8'); ?>">Teklif Al</a>
      <button class="eg112-toggle" id="eg112-toggle" type="button" aria-label="Menüyü aç" aria-expanded="false"><span></span><span></span><span></span></button>
    </div>
  </div>

  <div class="eg112-mobile-panel" id="eg112-mobile-panel">
    <div class="eg-shell-container eg112-mobile-panel__inner">
      <a href="/">Ana Sayfa</a>
      <button type="button" id="eg112-mobile-prefab" aria-expanded="false">Prefabrik Yapılar <span>+</span></button>
      <div class="eg112-mobile-sub" id="eg112-mobile-sub">
        <a href="<?php echo htmlspecialchars($eg112_prefabrik, ENT_QUOTES, 'UTF-8'); ?>"><strong>Tüm Prefabrik Yapılar</strong></a>
        <a href="<?php echo !empty($egeser_url_tek_katli) ? $egeser_url_tek_katli : '/tek-katli-prefabrik-evler'; ?>">Tek Katlı Prefabrik Evler</a>
        <a href="<?php echo !empty($egeser_url_cift_katli) ? $egeser_url_cift_katli : '/cift-katli-prefabrik-evler'; ?>">Çift Katlı Prefabrik Evler</a>
        <a href="<?php echo !empty($egeser_url_ofis_yonetim) ? $egeser_url_ofis_yonetim : '/prefabrik-ofis-ve-yonetim-binalari'; ?>">Ofis & Yönetim Binaları</a>
        <a href="<?php echo !empty($egeser_url_yatakhane) ? $egeser_url_yatakhane : '/prefabrik-yatakhane-binalari'; ?>">Yatakhane Binaları</a>
        <a href="<?php echo !empty($egeser_url_yemekhane) ? $egeser_url_yemekhane : '/prefabrik-yemekhane-binalari'; ?>">Yemekhane Binaları</a>
        <a href="<?php echo !empty($egeser_url_santiye) ? $egeser_url_santiye : '/prefabrik-santiye-yapilari'; ?>">Şantiye Yapıları</a>
        <a href="<?php echo !empty($egeser_url_sosyal_tesis) ? $egeser_url_sosyal_tesis : '/prefabrik-sosyal-tesis-yapilari'; ?>">Sosyal Tesis Yapıları</a>
        <a href="<?php echo !empty($egeser_url_ozel_proje) ? $egeser_url_ozel_proje : '/ozel-proje-prefabrik-yapilar'; ?>">Özel Proje Prefabrik Yapılar</a>
      </div>
      <a href="<?php echo htmlspecialchars($eg112_projeler, ENT_QUOTES, 'UTF-8'); ?>">Projelerimiz</a>
      <a href="<?php echo htmlspecialchars($eg112_teknik, ENT_QUOTES, 'UTF-8'); ?>">Teknik Bilgiler</a>
      <a href="<?php echo htmlspecialchars($eg112_kurumsal, ENT_QUOTES, 'UTF-8'); ?>">Hakkımızda</a>
      <a href="<?php echo htmlspecialchars($eg112_blog, ENT_QUOTES, 'UTF-8'); ?>">Blog</a>
      <a href="<?php echo htmlspecialchars($eg112_contact, ENT_QUOTES, 'UTF-8'); ?>">İletişim</a>
    </div>
  </div>
</header>

<?php
$eg17_email = !empty($egeser_email) ? trim($egeser_email) : '';
?>
<div class="eg17-float-stack" role="group" aria-label="Hızlı iletişim seçenekleri">
  <?php if ($eg17_email) { ?>
  <a class="eg17-float-wa eg17-float-wa--mail" href="mailto:<?php echo htmlspecialchars($eg17_email, ENT_QUOTES, 'UTF-8'); ?>" aria-label="E-posta gönderin: <?php echo htmlspecialchars($eg17_email, ENT_QUOTES, 'UTF-8'); ?>">
    <span class="eg17-float-wa__label">E-posta Gönder</span>
    <span class="eg17-float-wa__icon" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2Z"></path>
        <path d="m22 6-10 7L2 6"></path>
      </svg>
    </span>
  </a>
  <?php } ?>
  <a class="eg17-float-wa eg17-float-wa--call" href="<?php echo $eg112_phone_href; ?>" aria-label="Hemen arayın: <?php echo htmlspecialchars($eg112_phone, ENT_QUOTES, 'UTF-8'); ?>">
    <span class="eg17-float-wa__label"><?php echo htmlspecialchars($eg112_phone, ENT_QUOTES, 'UTF-8'); ?></span>
    <span class="eg17-float-wa__icon" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7"></path>
      </svg>
    </span>
  </a>
  <a class="eg17-float-wa eg17-float-wa--wa"
     href="https://wa.me/<?php echo htmlspecialchars($egeser_whatsapp, ENT_QUOTES, 'UTF-8'); ?>?text=<?php echo rawurlencode('Merhaba, prefabrik yapı modelleriniz hakkında bilgi ve teklif almak istiyorum.'); ?>"
     target="_blank"
     rel="noopener"
     aria-label="WhatsApp üzerinden prefabrik yapı teklifi alın">
    <span class="eg17-float-wa__label">WhatsApp'tan Teklif Al</span>
    <span class="eg17-float-wa__icon" aria-hidden="true">
      <svg viewBox="0 0 32 32">
        <path d="M19.11 17.38c-.26-.13-1.54-.76-1.78-.85-.24-.09-.42-.13-.59.13-.17.26-.68.85-.83 1.02-.15.17-.31.2-.57.07-.26-.13-1.1-.4-2.09-1.29-.77-.69-1.29-1.54-1.44-1.8-.15-.26-.02-.4.11-.53.12-.12.26-.31.39-.46.13-.15.17-.26.26-.44.09-.17.04-.33-.02-.46-.07-.13-.59-1.43-.81-1.96-.21-.51-.43-.44-.59-.45h-.5c-.17 0-.46.07-.7.33-.24.26-.92.9-.92 2.2 0 1.3.94 2.55 1.07 2.72.13.17 1.85 2.83 4.49 3.97.63.27 1.12.43 1.5.55.63.2 1.2.17 1.65.1.5-.07 1.54-.63 1.76-1.24.22-.61.22-1.13.15-1.24-.06-.11-.24-.17-.5-.3z"/>
        <path d="M16.02 3.2c-7.06 0-12.8 5.74-12.8 12.8 0 2.26.59 4.46 1.72 6.4L3.1 28.8l6.55-1.72A12.76 12.76 0 0 0 16.02 28.8c7.06 0 12.8-5.74 12.8-12.8S23.08 3.2 16.02 3.2zm0 23.44c-2.08 0-4.11-.61-5.84-1.77l-.42-.25-3.89 1.02 1.04-3.79-.27-.44A10.59 10.59 0 0 1 5.38 16c0-5.87 4.77-10.64 10.64-10.64S26.66 10.13 26.66 16 21.89 26.64 16.02 26.64z"/>
      </svg>
    </span>
  </a>
</div>

<a class="eg17-float-contact" href="<?php echo htmlspecialchars($eg112_contact, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Bize ulaşın">
  <span class="eg17-float-contact__icon" aria-hidden="true">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"></path>
    </svg>
  </span>
  <span class="eg17-float-contact__label">Bize Ulaşın</span>
</a>

<script>
(function(){
  'use strict';
  var mega = document.getElementById('eg112-mega');
  var megaToggle = document.getElementById('eg112-mega-toggle');
  var menuToggle = document.getElementById('eg112-toggle');
  var mobilePanel = document.getElementById('eg112-mobile-panel');
  var mobilePrefab = document.getElementById('eg112-mobile-prefab');
  var mobileSub = document.getElementById('eg112-mobile-sub');

  function setMega(open){
    if(!mega || !megaToggle) return;
    mega.classList.toggle('is-open', open);
    megaToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  if(megaToggle){
    megaToggle.addEventListener('click', function(e){ e.stopPropagation(); setMega(!mega.classList.contains('is-open')); });
  }
  document.addEventListener('click', function(e){
    if(mega && !mega.contains(e.target)) setMega(false);
  });
  document.addEventListener('keydown', function(e){ if(e.key === 'Escape') setMega(false); });

  if(menuToggle && mobilePanel){
    menuToggle.addEventListener('click', function(){
      var open = mobilePanel.classList.toggle('is-open');
      menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }
  if(mobilePrefab && mobileSub){
    mobilePrefab.addEventListener('click', function(){
      var open = mobileSub.classList.toggle('is-open');
      mobilePrefab.setAttribute('aria-expanded', open ? 'true' : 'false');
      var s = mobilePrefab.querySelector('span'); if(s) s.textContent = open ? '−' : '+';
    });
  }

  /* Active menu state without changing OpenCart core/controller */
  (function(){
    var path = (window.location.pathname || '').toLowerCase();
    var search = (window.location.search || '').toLowerCase();
    var full = path + search;
    var key = '';

    if (full.indexOf('route=common/home') !== -1 || (path === '/' || path === '')) key = 'home';
    else if (full.indexOf('route=product/category') !== -1 || full.indexOf('prefabrik') !== -1 && full.indexOf('teknik') === -1) key = 'prefabrik';
    if (path.indexOf('/projelerimiz') !== -1) key = 'projeler';
    if (path.indexOf('/teknik-bilgiler') !== -1) key = 'teknik';
    if (path.indexOf('/hakkimizda') !== -1) key = 'kurumsal';
    if (path === '/blog' || path.indexOf('/blog/') === 0 || full.indexOf('route=information/egeser_blog') !== -1) key = 'blog';
    if (full.indexOf('route=information/contact') !== -1 || path.indexOf('/iletisim') !== -1) key = 'iletisim';

    if(key){
      var active = document.querySelector('[data-eg-nav="' + key + '"]');
      if(active) active.classList.add('is-active');
    }
  })();
})();
</script>


<script>
(function(){
  'use strict';
  function fixFooterLogo(){
    var footer = document.querySelector('footer');
    if(!footer) return;

    var imgs = footer.querySelectorAll('img');
    Array.prototype.forEach.call(imgs,function(img){
      var src = (img.getAttribute('src') || '').toLowerCase();
      var alt = (img.getAttribute('alt') || '').toLowerCase();

      if(src.indexOf('egeser') !== -1 || alt.indexOf('egeser') !== -1 || src.indexOf('logo') !== -1){
        img.classList.add('eg17-footer-logo-img');

        var parent = img.parentElement;
        if(parent && !parent.classList.contains('eg17-footer-logo-plate')){
          parent.classList.add('eg17-footer-logo-plate');
        }
      }
    });
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', fixFooterLogo);
  } else {
    fixFooterLogo();
  }
})();
</script>
