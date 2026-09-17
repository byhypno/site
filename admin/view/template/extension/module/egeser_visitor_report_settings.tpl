<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid">
    <h1><?php echo $heading_title; ?></h1>
    <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
  </div></div>

  <div class="container-fluid">
    <?php include(DIR_TEMPLATE . 'extension/module/egeser_visitor_report_tabs.tpl'); ?>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-database"></i> Veri Saklama (Retention)</h3></div>
      <div class="panel-body">
        <form method="post" action="<?php echo $action; ?>" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-3 control-label">Event Saklama Süresi (gün)</label>
            <div class="col-sm-3">
              <input type="number" min="7" max="3650" name="egeser_visitor_event_retention" value="<?php echo (int)$egeser_visitor_event_retention; ?>" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label">Session Saklama Süresi (gün)</label>
            <div class="col-sm-3">
              <input type="number" min="7" max="3650" name="egeser_visitor_session_retention" value="<?php echo (int)$egeser_visitor_session_retention; ?>" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label">Anonim Ziyaretçi Saklama Süresi (gün)</label>
            <div class="col-sm-3">
              <input type="number" min="30" max="3650" name="egeser_visitor_retention" value="<?php echo (int)$egeser_visitor_retention; ?>" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3">
              <p class="help-block">Bu sürelerden eski kayıtlar, aşağıdaki "Şimdi Temizle" butonuyla veya kurulu bir cron görevi ile (index.php?route=extension/module/egeser_visitor_report/cron, X-Egeser-Cron-Key header'ı Egeser Site Kontrol Merkezi'ndeki cron anahtarıyla aynı) toplu (batch) halde silinir.</p>
              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Kaydet</button>
            </div>
          </div>
        </form>
        <hr>
        <form method="post" action="<?php echo $action; ?>">
          <input type="hidden" name="egeser_visitor_cleanup_now" value="1">
          <button type="submit" class="btn btn-warning" onclick="return confirm('Belirtilen sürelerden eski ziyaretçi/oturum/event kayıtları kalıcı olarak silinecek. Devam edilsin mi?');"><i class="fa fa-trash"></i> Şimdi Temizle</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
