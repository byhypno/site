<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid">
    <h1><?php echo $heading_title; ?></h1>
    <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
  </div></div>

  <div class="container-fluid">
    <?php include(DIR_TEMPLATE . 'extension/module/egeser_visitor_report_tabs.tpl'); ?>

    <div class="row">
      <div class="col-sm-3"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo (int)$kpis['visitors']; ?></h2><small>Toplam Ziyaretçi</small></div></div></div>
      <div class="col-sm-3"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo count($il_ranking); ?></h2><small>İl Tespit Edildi</small></div></div></div>
      <div class="col-sm-6">
        <div class="panel panel-default"><div class="panel-body">
          <small class="text-muted">İl tespiti ziyaretçinin IP adresinden tek seferlik yapılır, ham IP hiçbir yerde saklanmaz. Mobil operatör IP'leri bazen gerçek konumdan farklı (operatörün bölgesel ağ geçidine ait) bir il gösterebilir — özellikle büyük iller için bu normaldir.</small>
        </div></div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-map-marker"></i> İl Bazlı Ziyaretçi Dağılımı</h3></div>
      <div class="panel-body">
        <?php include(DIR_TEMPLATE . 'common/egeser_il_map.tpl'); ?>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
