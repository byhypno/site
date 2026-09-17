<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid">
    <div class="pull-right">
      <button type="submit" form="form-egeser-health" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
      <a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
    </div>
    <h1><?php echo $heading_title; ?></h1>
    <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
  </div></div>

  <div class="container-fluid">
    <?php if ($error_warning) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div><?php } ?>
    <?php if ($success) { ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div><?php } ?>

    <div class="row">
      <?php foreach ($report as $item) { $class = $item['status']=='green'?'success':($item['status']=='red'?'danger':'warning'); ?>
      <div class="col-sm-6 col-lg-4">
        <div class="panel panel-<?php echo $class; ?>">
          <div class="panel-heading"><strong><?php echo $item['label']; ?></strong></div>
          <div class="panel-body"><?php echo $item['detail']; ?></div>
        </div>
      </div>
      <?php } ?>
    </div>



    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-shield"></i> Güvenlik / SEO Spam Kontrolü</h3></div>
      <div class="panel-body">
        <div class="row text-center">
          <div class="col-sm-3"><h3><?php echo (int)$security_stats['queries_24h']; ?></h3><small>Şüpheli Sorgu / 24 Saat</small></div>
          <div class="col-sm-3"><h3><?php echo (int)$security_stats['unique_queries']; ?></h3><small>Benzersiz Spam Sorgu</small></div>
          <div class="col-sm-3"><h3><?php echo (int)$security_stats['total_hits']; ?></h3><small>Toplam Spam İsteği</small></div>
          <div class="col-sm-3"><h3><?php echo $egeser_security_search_noindex ? 'AÇIK' : 'KAPALI'; ?></h3><small>Search Noindex</small></div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead><tr><th>Sorgu</th><th>Tekrar</th><th>Son Görülme</th></tr></thead>
          <tbody>
          <?php if ($recent_spam) { foreach ($recent_spam as $spam) { ?>
            <tr>
              <td style="max-width:700px;word-break:break-word;"><?php echo htmlspecialchars($spam['query_text'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo (int)$spam['hits']; ?></td>
              <td><?php echo $spam['last_seen']; ?></td>
            </tr>
          <?php } } else { ?><tr><td colspan="3" class="text-center">Henüz şüpheli arama sorgusu kaydı yok.</td></tr><?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-random"></i> Redirect Zinciri Denetimi</h3></div>
      <div class="panel-body">
        <div class="input-group">
          <input type="url" id="eg-redirect-url" class="form-control" placeholder="https://www.egeserprefabrik.com.tr/64-m2-tek-katli-prefabrik-ev">
          <span class="input-group-btn"><button class="btn btn-primary" type="button" id="eg-redirect-audit">Redirect Kontrol Et</button></span>
        </div>
        <p id="eg-redirect-result" style="margin-top:10px"></p>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead><tr><th>Kaynak</th><th>Son URL</th><th>Durum</th><th>Hop</th><th>Tarih</th></tr></thead>
          <tbody>
          <?php if ($recent_redirect_audits) { foreach ($recent_redirect_audits as $audit) { ?>
            <tr>
              <td style="max-width:330px;word-break:break-all;"><?php echo htmlspecialchars($audit['source_url'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td style="max-width:330px;word-break:break-all;"><?php echo htmlspecialchars($audit['final_url'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo $audit['status']; ?></td>
              <td><?php echo (int)$audit['hop_count']; ?></td>
              <td><?php echo $audit['date_checked']; ?></td>
            </tr>
          <?php } } else { ?><tr><td colspan="5" class="text-center">Henüz redirect denetimi yapılmadı.</td></tr><?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-tachometer"></i> Performans / Core Web Vitals Hazırlık Kontrolü</h3></div>
      <div class="panel-body">
        <?php if ($latest_performance) { ?>
        <div class="row text-center">
          <div class="col-sm-2"><h3><?php echo (int)$latest_performance['score']; ?>%</h3><small>Performans Skoru</small></div>
          <div class="col-sm-2"><h3><?php echo (int)$latest_performance['pages_tested']; ?></h3><small>Örnek Sayfa</small></div>
          <div class="col-sm-2"><h3><?php echo (int)$latest_performance['avg_response_ms']; ?> ms</h3><small>Ort. HTML Yanıtı</small></div>
          <div class="col-sm-2"><h3><?php echo (int)$latest_performance['avg_html_kb']; ?> KB</h3><small>Ort. HTML</small></div>
          <div class="col-sm-2"><h3><?php echo (int)$latest_performance['issues_found']; ?></h3><small>Tespit</small></div>
        </div>
        <?php } else { ?>
        <p>Henüz performans denetimi yapılmadı. Aşağıdaki <strong>Performans Denetimi</strong> butonunu kullanın.</p>
        <?php } ?>
        <div class="alert alert-info" style="margin-top:15px;margin-bottom:0">
          Bu skor gerçek kullanıcı LCP / INP / CLS verisi değildir. Sunucu yanıtı, HTML ağırlığı, görsel boyut rezervasyonu,
          lazy-load adayları, DOM büyüklüğü ve render-blocking script risklerini erken tespit eder.
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead><tr><th>ID</th><th>Tip</th><th>Durum</th><th>Skor</th><th>Sayfa</th><th>Yanıt</th><th>HTML</th><th>Tespit</th><th>Tarih</th></tr></thead>
          <tbody>
          <?php if ($recent_performance_runs) { foreach ($recent_performance_runs as $run) { ?>
            <tr>
              <td><?php echo $run['performance_id']; ?></td>
              <td><?php echo $run['run_type']; ?></td>
              <td><?php echo $run['status']; ?></td>
              <td><strong><?php echo $run['score']; ?>%</strong></td>
              <td><?php echo $run['pages_tested']; ?></td>
              <td><?php echo $run['avg_response_ms']; ?> ms</td>
              <td><?php echo $run['avg_html_kb']; ?> KB</td>
              <td><?php echo $run['issues_found']; ?></td>
              <td><?php echo $run['date_started']; ?></td>
            </tr>
          <?php } } else { ?>
            <tr><td colspan="9" class="text-center">Performans geçmişi yok.</td></tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-line-chart"></i> Lead Özeti</h3></div>
      <div class="panel-body">
        <div class="row text-center">
          <div class="col-sm-2"><h3><?php echo $lead_stats['today']; ?></h3><small>Bugün</small></div>
          <div class="col-sm-2"><h3><?php echo $lead_stats['week']; ?></h3><small>7 Gün</small></div>
          <div class="col-sm-2"><h3><?php echo $lead_stats['individual']; ?></h3><small>Bireysel / 30 Gün</small></div>
          <div class="col-sm-2"><h3><?php echo $lead_stats['corporate']; ?></h3><small>Kurumsal / 30 Gün</small></div>
          <div class="col-sm-2"><h3><?php echo $lead_stats['failed']; ?></h3><small>Mail Hatası / 7 Gün</small></div>
        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-heartbeat"></i> Sağlık Kontrol Geçmişi</h3></div>
      <div class="table-responsive"><table class="table table-bordered table-hover">
        <thead><tr><th>ID</th><th>Tip</th><th>Durum</th><th>Skor</th><th>Sayfa</th><th>Kontrol</th><th>Sorun</th><th>Tarih</th></tr></thead>
        <tbody>
        <?php if ($recent_runs) { foreach ($recent_runs as $run) { ?>
          <tr>
            <td><?php echo $run['run_id']; ?></td><td><?php echo $run['run_type']; ?></td><td><?php echo $run['status']; ?></td>
            <td><strong><?php echo $run['score']; ?>%</strong></td><td><?php echo $run['pages_scanned']; ?></td>
            <td><?php echo $run['links_checked']; ?></td><td><?php echo $run['issues_found']; ?></td><td><?php echo $run['date_started']; ?></td>
          </tr>
        <?php } } else { ?><tr><td colspan="8" class="text-center">Henüz sağlık kontrol kaydı yok.</td></tr><?php } ?>
        </tbody>
      </table></div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-unlink"></i> Açık Kırık Link / Görsel Sorunları</h3></div>
      <div class="table-responsive"><table class="table table-bordered table-hover">
        <thead><tr><th>Tür</th><th>HTTP</th><th>Kaynak Sayfa</th><th>Sorunlu Hedef</th><th>Tekrar</th><th>Son Görülme</th></tr></thead>
        <tbody>
        <?php if ($open_issues) { foreach ($open_issues as $issue) { ?>
          <tr>
            <td><?php echo $issue['issue_type']; ?></td><td><?php echo $issue['http_code']; ?></td>
            <td style="max-width:300px;word-break:break-all;"><?php echo htmlspecialchars($issue['source_url'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td style="max-width:360px;word-break:break-all;"><?php echo htmlspecialchars($issue['target_url'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo $issue['hits']; ?></td><td><?php echo $issue['last_seen']; ?></td>
          </tr>
        <?php } } else { ?><tr><td colspan="6" class="text-center">Açık kırık link/görsel sorunu yok veya henüz tarama yapılmadı.</td></tr><?php } ?>
        </tbody>
      </table></div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title">Son Lead Kayıtları</h3></div>
      <div class="table-responsive"><table class="table table-bordered table-hover">
        <thead><tr><th>ID</th><th>Tip</th><th>Firma / İsim</th><th>Telefon</th><th>E-posta</th><th>Proje</th><th>Ürün</th><th>Lokasyon</th><th>Mail</th><th>Tarih</th></tr></thead>
        <tbody>
        <?php if ($recent_leads) { foreach ($recent_leads as $lead) { ?>
          <tr>
            <td><?php echo $lead['lead_id']; ?></td><td><?php echo $lead['customer_type']; ?></td>
            <td><?php echo $lead['company'] ? $lead['company'].' / '.$lead['name'] : $lead['name']; ?></td>
            <td><?php echo $lead['phone_masked']; ?></td><td><?php echo $lead['email_masked']; ?></td>
            <td><?php echo htmlspecialchars($lead['project_type'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo $lead['product_name'] ? htmlspecialchars($lead['product_name'], ENT_QUOTES, 'UTF-8') : '-'; ?></td>
            <td><?php echo $lead['location'] ? htmlspecialchars($lead['location'], ENT_QUOTES, 'UTF-8') : '-'; ?></td>
            <td><?php echo $lead['mail_status']; ?></td><td><?php echo $lead['date_added']; ?></td>
          </tr>
        <?php } } else { ?><tr><td colspan="10" class="text-center">Henüz kayıt yok.</td></tr><?php } ?>
        </tbody>
      </table></div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-cog"></i> Ayarlar, Cron ve Dönüşüm Ölçümü</h3></div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-egeser-health" class="form-horizontal">

          <div class="form-group"><label class="col-sm-3 control-label"><?php echo $entry_status; ?></label><div class="col-sm-9">
            <select name="egeser_health_status" class="form-control"><option value="1" <?php echo $egeser_health_status?'selected':''; ?>>Açık</option><option value="0" <?php echo !$egeser_health_status?'selected':''; ?>>Kapalı</option></select>
          </div></div>

          <div class="form-group"><label class="col-sm-3 control-label"><?php echo $entry_whatsapp; ?></label><div class="col-sm-9">
            <input type="text" name="egeser_health_whatsapp" value="<?php echo htmlspecialchars($egeser_health_whatsapp, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ülke kodu ile numara (örn. 90...) " class="form-control" />
          </div></div>

          <div class="form-group"><label class="col-sm-3 control-label"><?php echo $entry_lead_recipient; ?></label><div class="col-sm-9">
            <input type="email" name="egeser_health_lead_recipient" value="<?php echo htmlspecialchars($egeser_health_lead_recipient, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" />
          </div></div>

          <div class="form-group"><label class="col-sm-3 control-label"><?php echo $entry_retention; ?></label><div class="col-sm-9">
            <input type="number" min="30" max="730" name="egeser_health_retention_days" value="<?php echo (int)$egeser_health_retention_days; ?>" class="form-control" />
            <p class="help-block"><?php echo $help_retention; ?></p>
          </div></div>


          <hr>
          <h4>Site Linkleri / Sosyal Medya</h4>
          <div class="alert alert-info">URL bilinmeyen bağlantılar temada gösterilmez; böylece href="#" gibi ölü link bırakılmaz.</div>
          <div class="form-group"><label class="col-sm-3 control-label">Instagram</label><div class="col-sm-9"><input type="url" name="egeser_site_instagram" value="<?php echo htmlspecialchars($egeser_site_instagram, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="https://..."></div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Facebook</label><div class="col-sm-9"><input type="url" name="egeser_site_facebook" value="<?php echo htmlspecialchars($egeser_site_facebook, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="https://..."></div></div>
          <div class="form-group"><label class="col-sm-3 control-label">TikTok</label><div class="col-sm-9"><input type="url" name="egeser_site_tiktok" value="<?php echo htmlspecialchars($egeser_site_tiktok, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="https://..."></div></div>
          <div class="form-group"><label class="col-sm-3 control-label">YouTube</label><div class="col-sm-9"><input type="url" name="egeser_site_youtube" value="<?php echo htmlspecialchars($egeser_site_youtube, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="https://..."></div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Blog URL</label><div class="col-sm-9"><input type="url" name="egeser_site_blog_url" value="<?php echo htmlspecialchars($egeser_site_blog_url, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="https://www.egeserprefabrik.com.tr/..."></div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Teknik Bilgiler URL</label><div class="col-sm-9"><input type="url" name="egeser_site_technical_url" value="<?php echo htmlspecialchars($egeser_site_technical_url, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="https://www.egeserprefabrik.com.tr/..."></div></div>

          <hr>
          <h4>Günlük Sağlık Kontrolü</h4>
          <div class="form-group"><label class="col-sm-3 control-label">Tarama Sayfa Limiti</label><div class="col-sm-9">
            <input type="number" min="5" max="100" name="egeser_health_scan_max_pages" value="<?php echo (int)$egeser_health_scan_max_pages; ?>" class="form-control" />
            <p class="help-block">Günlük taramada en fazla kaç HTML sayfasının inceleneceği. Başlangıç için 40 önerilir.</p>
          </div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Cron Güvenlik Anahtarı</label><div class="col-sm-9">
            <input type="text" readonly name="egeser_health_cron_key" value="<?php echo htmlspecialchars($egeser_health_cron_key, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" />
            <p class="help-block">Bu anahtarı paylaşmayın. URL parametresi yerine HTTP header ile kullanılması önerilir.</p>
          </div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Cron Endpoint</label><div class="col-sm-9">
            <input type="text" readonly value="<?php echo htmlspecialchars($cron_endpoint, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" />
          </div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Örnek Cron Komutu</label><div class="col-sm-9">
            <textarea readonly rows="3" class="form-control"><?php echo htmlspecialchars($cron_command, ENT_QUOTES, 'UTF-8'); ?></textarea>
            <p class="help-block">Hosting cron panelinde günde 1 kez çalıştırın. Önerilen saat: trafik düşükken 03:30–05:30.</p>
          </div></div>

          <hr>
          <h4>Performans Ayarları</h4>
          <div class="form-group"><label class="col-sm-3 control-label">Cron Performans Örneklemesi</label><div class="col-sm-9">
            <select name="egeser_performance_cron_status" class="form-control">
              <option value="1" <?php echo $egeser_performance_cron_status?'selected':''; ?>>Açık</option>
              <option value="0" <?php echo !$egeser_performance_cron_status?'selected':''; ?>>Kapalı</option>
            </select>
            <p class="help-block">Günlük sağlık cron'u ana sayfa + örnek kategori + örnek ürün üzerinde hafif performans kontrolü yapar.</p>
          </div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Content Visibility</label><div class="col-sm-9">
            <select name="egeser_performance_content_visibility" class="form-control">
              <option value="1" <?php echo $egeser_performance_content_visibility?'selected':''; ?>>Açık</option>
              <option value="0" <?php echo !$egeser_performance_content_visibility?'selected':''; ?>>Kapalı</option>
            </select>
            <p class="help-block">Destekleyen tarayıcılarda aşağıdaki içerik bölümlerinin ilk render maliyetini azaltmak için kullanılır.</p>
          </div></div>

          <hr>
          <h4>Güvenlik / SEO Spam Ayarları</h4>

          <div class="form-group"><label class="col-sm-3 control-label">Arama Sonuçlarını Noindex Yap</label><div class="col-sm-9">
            <select name="egeser_security_search_noindex" class="form-control">
              <option value="1" <?php echo $egeser_security_search_noindex?'selected':''; ?>>Açık</option>
              <option value="0" <?php echo !$egeser_security_search_noindex?'selected':''; ?>>Kapalı</option>
            </select>
            <p class="help-block">OpenCart dahili arama sonuçları için noindex,follow uygular. SEO spam parametrelerinin indexlenmesini engellemeye yardımcı olur.</p>
          </div></div>

          <div class="form-group"><label class="col-sm-3 control-label">Arama Rate Limit / Dakika</label><div class="col-sm-9">
            <input type="number" min="5" max="300" name="egeser_security_search_rate_limit" value="<?php echo (int)$egeser_security_search_rate_limit; ?>" class="form-control">
            <p class="help-block">Aynı IP hash için dakikadaki arama isteği sınırı. Başlangıç için 30.</p>
          </div></div>

          <div class="form-group"><label class="col-sm-3 control-label">Spam Loglama</label><div class="col-sm-9">
            <select name="egeser_security_spam_logging" class="form-control">
              <option value="1" <?php echo $egeser_security_spam_logging?'selected':''; ?>>Açık</option>
              <option value="0" <?php echo !$egeser_security_spam_logging?'selected':''; ?>>Kapalı</option>
            </select>
          </div></div>

          <input type="hidden" name="egeser_security_ip_salt" value="<?php echo htmlspecialchars($egeser_security_ip_salt, ENT_QUOTES, 'UTF-8'); ?>">

          <hr>
          <h4>GA4 / Meta / Google Ads Dönüşüm Ölçümü</h4>
          <div class="alert alert-info">Bu alan varsayılan olarak kapalıdır. Mevcut Google Analytics veya Meta Pixel kurulumu varsa çift ölçümü önlemek için önce kontrol edin.</div>

          <div class="form-group"><label class="col-sm-3 control-label">Dönüşüm Ölçümü</label><div class="col-sm-9">
            <select name="egeser_health_tracking_status" class="form-control"><option value="1" <?php echo $egeser_health_tracking_status?'selected':''; ?>>Açık</option><option value="0" <?php echo !$egeser_health_tracking_status?'selected':''; ?>>Kapalı</option></select>
          </div></div>
          <div class="form-group"><label class="col-sm-3 control-label">GA4 Measurement ID</label><div class="col-sm-9">
            <input type="text" name="egeser_health_ga4_id" value="<?php echo htmlspecialchars($egeser_health_ga4_id, ENT_QUOTES, 'UTF-8'); ?>" placeholder="G-XXXXXXXXXX" class="form-control" />
          </div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Meta Pixel ID</label><div class="col-sm-9">
            <input type="text" name="egeser_health_meta_pixel_id" value="<?php echo htmlspecialchars($egeser_health_meta_pixel_id, ENT_QUOTES, 'UTF-8'); ?>" placeholder="123456789012345" class="form-control" />
          </div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Google Ads ID</label><div class="col-sm-9">
            <input type="text" name="egeser_health_google_ads_id" value="<?php echo htmlspecialchars($egeser_health_google_ads_id, ENT_QUOTES, 'UTF-8'); ?>" placeholder="AW-123456789" class="form-control" />
          </div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Google Ads Lead Etiketi</label><div class="col-sm-9">
            <input type="text" name="egeser_health_google_ads_lead_label" value="<?php echo htmlspecialchars($egeser_health_google_ads_lead_label, ENT_QUOTES, 'UTF-8'); ?>" placeholder="AbCdEf..." class="form-control" />
            <p class="help-block">Form başarıyla gönderildiğinde GA4 generate_lead, Meta Lead ve tanımlıysa Google Ads conversion olayı tetiklenir. WhatsApp/telefon tıklamaları ayrıca ölçülür.</p>
          </div></div>
        </form>

        <button type="button" id="eg-run" class="btn btn-info"><i class="fa fa-refresh"></i> Hızlı Kontrol</button>
        <button type="button" id="eg-scan" class="btn btn-danger"><i class="fa fa-link"></i> Kırık Link / Görsel Tara</button>
        <button type="button" id="eg-mail" class="btn btn-warning"><i class="fa fa-envelope"></i> <?php echo $button_test_mail; ?></button>
        <button type="button" id="eg-performance" class="btn btn-success"><i class="fa fa-tachometer"></i> Performans Denetimi</button>
        <span id="eg-result" style="margin-left:10px"></span>
      </div>
    </div>
  </div>
</div>
<script>
(function(){
  function call(url,label){
    var el=document.getElementById('eg-result');
    el.textContent=label+' çalışıyor...';
    fetch(url,{credentials:'same-origin',headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(function(r){
        return r.text().then(function(text){
          if(!r.ok){throw new Error('HTTP '+r.status);}
          try{return JSON.parse(text);}
          catch(e){
            var clean=text.replace(/<[^>]*>/g,' ').replace(/\s+/g,' ').trim();
            throw new Error(clean ? clean.substring(0,180) : 'JSON yanıtı alınamadı');
          }
        });
      })
      .then(function(j){
        if(j.scan){
          el.textContent='Tamamlandı. Skor: '+j.score+'% · Sayfa: '+j.scan.pages_scanned+' · Kontrol: '+j.scan.links_checked+' · Sorun: '+j.scan.issues_found;
          // Açık sorunlar ve sağlık geçmişi server-side render edildiği için,
          // başarılı taramadan sonra sayfayı yenileyerek yeni kayıtları göster.
          if (j.success === true && j.scan.scan_valid !== false) {
            setTimeout(function(){ window.location.reload(); }, 900);
          }
        } else if(typeof j.pages_tested !== 'undefined'){
          el.textContent='Tamamlandı. Skor: '+(j.score||0)+'% · Sayfa: '+j.pages_tested+' · Ortalama yanıt: '+(j.avg_response_ms||0)+' ms · HTML: '+(j.avg_html_kb||0)+' KB · Tespit: '+(j.issues_found||0);
        } else {
          el.textContent=j.success===true?('Tamamlandı. Skor: '+(j.score||0)+'%'):(j.success||j.error||'Tamamlandı.');
        }
      })
      .catch(function(e){el.textContent='İstek başarısız: '+e.message;});
  }
  document.getElementById('eg-run').addEventListener('click',function(){call(<?php echo json_encode($run_url); ?>,'Hızlı kontrol');});
  document.getElementById('eg-scan').addEventListener('click',function(){if(confirm('Dahili link ve görsel taraması sunucuya ek yük oluşturabilir. Devam edilsin mi?'))call(<?php echo json_encode($scan_url); ?>,'Tarama');});
  document.getElementById('eg-mail').addEventListener('click',function(){call(<?php echo json_encode($mail_url); ?>,'Mail testi');});
  document.getElementById('eg-performance').addEventListener('click',function(){call(<?php echo json_encode($performance_url); ?>,'Performans denetimi');});
  document.getElementById('eg-redirect-audit').addEventListener('click',function(){
    var url=document.getElementById('eg-redirect-url').value.trim();
    var out=document.getElementById('eg-redirect-result');
    if(!url){out.textContent='URL girin.';return;}
    out.textContent='Redirect kontrol ediliyor...';
    fetch(<?php echo json_encode($redirect_audit_url); ?>+'&url='+encodeURIComponent(url),{credentials:'same-origin',headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(function(r){return r.json();})
      .then(function(j){
        if(j.error){out.textContent=j.error;return;}
        out.textContent='Durum: '+j.status+' · Hop: '+j.hop_count+' · Son URL: '+j.final_url;
      })
      .catch(function(){out.textContent='Redirect denetimi başarısız.';});
  });
}());
</script>
<?php echo $footer; ?>
