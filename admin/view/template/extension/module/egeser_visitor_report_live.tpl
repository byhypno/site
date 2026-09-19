<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid">
    <h1><?php echo $heading_title; ?></h1>
    <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
  </div></div>

  <div class="container-fluid">
    <?php include(DIR_TEMPLATE . 'extension/module/egeser_visitor_report_tabs.tpl'); ?>

    <p class="text-muted"><i class="fa fa-info-circle"></i> Son 5 dakika içinde aktif olan ziyaretçiler. 30 saniyede bir otomatik yenilenir. <span id="eg-live-count">(<?php echo count($live_visitors); ?>)</span></p>

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Ziyaretçi</th>
            <th>Kaynak</th>
            <th>Giriş Sayfası</th>
            <th>Şu An Baktığı Sayfa</th>
            <th>Cihaz</th>
            <th>Oturum Süresi</th>
            <th>Sayfa Gör.</th>
            <th>WhatsApp</th>
            <th>Teklif Formu</th>
          </tr>
        </thead>
        <tbody id="eg-live-body">
        <?php if ($live_visitors) { foreach ($live_visitors as $v) { ?>
          <tr>
            <td>#<?php echo strtoupper(substr((string)$v['visitor_token'], 0, 6)); ?></td>
            <td><?php echo htmlspecialchars(ucfirst($v['source']), ENT_QUOTES, 'UTF-8'); ?></td>
            <td style="max-width:220px;word-break:break-word;"><small><?php echo htmlspecialchars($v['landing_page'], ENT_QUOTES, 'UTF-8'); ?></small></td>
            <td><?php echo htmlspecialchars($v['current_page'] ?: $v['current_url'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($v['device_type'], ENT_QUOTES, 'UTF-8'); ?></td>
            <?php
            $eg_live_seconds = max(0, strtotime($v['last_activity']) - strtotime($v['started_at']));
            $eg_live_duration = sprintf('%d:%02d', floor($eg_live_seconds / 60), $eg_live_seconds % 60);
            ?>
            <td><?php echo $eg_live_duration; ?></td>
            <td><?php echo (int)$v['pageviews']; ?></td>
            <td><?php echo !empty($v['whatsapp_clicked']) ? '<i class="fa fa-check text-success"></i>' : '-'; ?></td>
            <td><?php echo !empty($v['quote_started']) ? '<i class="fa fa-check text-success"></i>' : '-'; ?></td>
          </tr>
        <?php } } else { ?>
          <tr><td colspan="9" class="text-center text-muted">Şu anda aktif ziyaretçi yok.</td></tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
(function(){
  var url = '<?php echo $live_data_url; ?>';
  var body = document.getElementById('eg-live-body');
  var countEl = document.getElementById('eg-live-count');

  function esc(s){ var d=document.createElement('div'); d.textContent=String(s==null?'':s); return d.innerHTML; }

  function render(data){
    if(!data || !data.visitors){ return; }
    countEl.textContent = '(' + data.count + ')';
    if(!data.visitors.length){
      body.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Şu anda aktif ziyaretçi yok.</td></tr>';
      return;
    }
    var html = '';
    data.visitors.forEach(function(v){
      html += '<tr>' +
        '<td>' + esc(v.visitor_label) + '</td>' +
        '<td>' + esc(v.source) + '</td>' +
        '<td style="max-width:220px;word-break:break-word;"><small>' + esc(v.landing_page) + '</small></td>' +
        '<td>' + esc(v.current_page) + '</td>' +
        '<td>' + esc(v.device_type) + '</td>' +
        '<td>' + esc(v.duration) + '</td>' +
        '<td>' + esc(v.pageviews) + '</td>' +
        '<td>' + (v.whatsapp_clicked ? '<i class="fa fa-check text-success"></i>' : '-') + '</td>' +
        '<td>' + (v.quote_started ? '<i class="fa fa-check text-success"></i>' : '-') + '</td>' +
        '</tr>';
    });
    body.innerHTML = html;
  }

  function poll(){
    fetch(url, {credentials:'same-origin'})
      .then(function(r){ return r.json(); })
      .then(render)
      .catch(function(){});
  }

  setInterval(poll, 30000);
})();
//--></script>
<?php echo $footer; ?>