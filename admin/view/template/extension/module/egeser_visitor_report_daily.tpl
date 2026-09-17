<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid">
    <h1><?php echo $heading_title; ?></h1>
    <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
  </div></div>

  <div class="container-fluid">
    <?php include(DIR_TEMPLATE . 'extension/module/egeser_visitor_report_tabs.tpl'); ?>

    <div class="row">
      <div class="col-sm-3 col-lg-2"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo (int)$kpis['visitors']; ?></h2><small>Ziyaretçi</small></div></div></div>
      <div class="col-sm-3 col-lg-2"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo (int)$kpis['sessions']; ?></h2><small>Oturum</small></div></div></div>
      <div class="col-sm-3 col-lg-2"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo (int)$kpis['pageviews']; ?></h2><small>Sayfa Görüntüleme</small></div></div></div>
      <div class="col-sm-3 col-lg-2"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo (int)$kpis['product_views']; ?></h2><small>Ürün Görüntüleme</small></div></div></div>
      <div class="col-sm-3 col-lg-2"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo (int)$kpis['whatsapp_clicks']; ?></h2><small>WhatsApp</small></div></div></div>
      <div class="col-sm-3 col-lg-2"><div class="panel panel-success"><div class="panel-body text-center"><h2><?php echo (int)$kpis['conversions']; ?></h2><small>Dönüşüm (%<?php echo $kpis['conversion_rate']; ?>)</small></div></div></div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Saatlik Trafik</h3></div>
      <div class="panel-body"><div id="eg-chart-hourly" style="width:100%;height:260px;"></div></div>
    </div>

    <div class="row">
      <div class="col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-pie-chart"></i> Kaynak Dağılımı</h3></div>
          <div class="panel-body"><div id="eg-chart-sources" style="width:100%;height:220px;"></div></div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-pie-chart"></i> Cihaz Dağılımı</h3></div>
          <div class="panel-body"><div id="eg-chart-devices" style="width:100%;height:220px;"></div></div>
        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-cube"></i> En Çok Görüntülenen Ürünler</h3></div>
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead><tr><th>Ürün</th><th>Görüntülenme</th><th>Benzersiz Ziyaretçi</th></tr></thead>
          <tbody>
          <?php if ($top_products) { foreach ($top_products as $p) { ?>
            <tr>
              <td><?php echo htmlspecialchars($p['name'] ?: ('#' . $p['entity_id']), ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo (int)$p['views']; ?></td>
              <td><?php echo (int)$p['visitors']; ?></td>
            </tr>
          <?php } } else { ?><tr><td colspan="3" class="text-center text-muted">Veri yok.</td></tr><?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.js"></script>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.pie.js"></script>
<script type="text/javascript"><!--
(function(){
  var hourly = <?php echo json_encode(array_values($hourly)); ?>;
  var hourlyData = hourly.map(function(v,i){ return [i, v]; });

  $.plot('#eg-chart-hourly', [{ data: hourlyData, bars: { show: true, fill: true, barWidth: 0.6, align: 'center' }, color: '#1065D2' }], {
    xaxis: { tickDecimals: 0, min: -0.5, max: 23.5 },
    yaxis: { tickDecimals: 0, min: 0 },
    grid: { backgroundColor: '#FFFFFF' }
  });

  var sources = <?php echo json_encode($sources); ?>;
  var sourceData = sources.map(function(s){ return { label: s.source, data: parseInt(s.total, 10) }; });
  if (sourceData.length) {
    $.plot('#eg-chart-sources', sourceData, { series: { pie: { show: true, label: { show: true } } } });
  }

  var devices = <?php echo json_encode($devices); ?>;
  var deviceData = devices.map(function(d){ return { label: d.device_type, data: parseInt(d.total, 10) }; });
  if (deviceData.length) {
    $.plot('#eg-chart-devices', deviceData, { series: { pie: { show: true, label: { show: true } } } });
  }
})();
//--></script>
<?php echo $footer; ?>
