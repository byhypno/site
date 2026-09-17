<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-lead" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
        <a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i> <?php echo $button_cancel; ?></a>
      </div>
      <h1><i class="fa fa-clipboard"></i> Teklif #<?php echo (int)$lead['lead_id']; ?></h1>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($success) { ?><div class="alert alert-success"><?php echo $success; ?></div><?php } ?>
    <?php if ($error_warning) { ?><div class="alert alert-danger"><?php echo $error_warning; ?></div><?php } ?>

    <div class="row">
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-user"></i> Talep Detayı</h3></div>
          <div class="panel-body">
            <table class="table table-striped">
              <tr><th style="width:220px">Tarih</th><td><?php echo htmlspecialchars($lead['date_added'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>Müşteri Tipi</th><td><?php echo htmlspecialchars($lead['customer_type'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>Firma</th><td><?php echo htmlspecialchars($lead['company'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>Ad Soyad</th><td><?php echo htmlspecialchars($lead['name'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>Telefon</th><td><a href="tel:<?php echo htmlspecialchars($lead['phone'],ENT_QUOTES,'UTF-8'); ?>"><?php echo htmlspecialchars($lead['phone'],ENT_QUOTES,'UTF-8'); ?></a></td></tr>
              <tr><th>E-posta</th><td><?php echo htmlspecialchars($lead['email'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>Yapı Türü</th><td><?php echo htmlspecialchars($lead['project_type'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>Lokasyon</th><td><?php echo htmlspecialchars($lead['location'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>Yaklaşık m²</th><td><?php echo htmlspecialchars($lead['area'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>Ürün</th><td><?php echo htmlspecialchars($lead['product_name'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>Mesaj</th><td><?php echo nl2br(htmlspecialchars($lead['message'],ENT_QUOTES,'UTF-8')); ?></td></tr>
              <tr><th>Kaynak</th><td><?php echo htmlspecialchars($lead['source'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>Sayfa URL</th><td style="word-break:break-all"><?php echo htmlspecialchars($lead['page_url'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>UTM</th><td><?php echo htmlspecialchars($lead['utm_source'].' / '.$lead['utm_medium'].' / '.$lead['utm_campaign'],ENT_QUOTES,'UTF-8'); ?></td></tr>
              <tr><th>Mail Durumu</th><td><?php echo htmlspecialchars($lead['mail_status'],ENT_QUOTES,'UTF-8'); ?><?php if($lead['mail_error']){ ?><br><small class="text-danger"><?php echo htmlspecialchars($lead['mail_error'],ENT_QUOTES,'UTF-8'); ?></small><?php } ?></td></tr>
              <tr><th>KVKK / İletişim Onayı</th><td><?php echo (int)$lead['consent'] ? '<span class="label label-success">Var</span>' : '<span class="label label-danger">Yok</span>'; ?></td></tr>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <form action="<?php echo $action; ?>" method="post" id="form-lead">
          <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-tasks"></i> Satış Takibi</h3></div>
            <div class="panel-body">
              <div class="form-group">
                <label>Durum</label>
                <select name="lead_status" class="form-control">
                  <?php foreach($statuses as $status){ ?><option value="<?php echo $status; ?>"<?php echo $lead['lead_status']===$status?' selected="selected"':''; ?>><?php echo $status; ?></option><?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label>Atanan Personel</label>
                <input type="text" name="assigned_to" value="<?php echo htmlspecialchars($lead['assigned_to'],ENT_QUOTES,'UTF-8'); ?>" class="form-control" placeholder="Örn. Mehmet / Satış 1">
              </div>
              <div class="form-group">
                <label>İç Not</label>
                <textarea name="admin_note" rows="8" class="form-control" placeholder="Müşteriyle görüşme notları..."><?php echo htmlspecialchars($lead['admin_note'],ENT_QUOTES,'UTF-8'); ?></textarea>
              </div>
              <?php if($lead['date_modified']){ ?><p class="text-muted"><small>Son güncelleme: <?php echo htmlspecialchars($lead['date_modified'],ENT_QUOTES,'UTF-8'); ?></small></p><?php } ?>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
