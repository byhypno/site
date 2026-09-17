<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $export; ?>" class="btn btn-success" data-toggle="tooltip" title="CSV Dışa Aktar"><i class="fa fa-file-excel-o"></i> CSV</a>
      </div>
      <h1><i class="fa fa-clipboard"></i> <?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($success) { ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div><?php } ?>
    <?php if ($warning) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $warning; ?></div><?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-filter"></i> Filtreler</h3></div>
      <div class="panel-body">
        <form method="get" action="index.php" class="row">
          <input type="hidden" name="route" value="sale/egeser_lead">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
          <div class="col-md-2"><label>Ad / Firma</label><input type="text" name="filter_name" value="<?php echo htmlspecialchars($filter_name,ENT_QUOTES,'UTF-8'); ?>" class="form-control"></div>
          <div class="col-md-2"><label>Telefon</label><input type="text" name="filter_phone" value="<?php echo htmlspecialchars($filter_phone,ENT_QUOTES,'UTF-8'); ?>" class="form-control"></div>
          <div class="col-md-2"><label>Lokasyon</label><input type="text" name="filter_location" value="<?php echo htmlspecialchars($filter_location,ENT_QUOTES,'UTF-8'); ?>" class="form-control"></div>
          <div class="col-md-2"><label>Durum</label><select name="filter_status" class="form-control"><option value="">Tümü</option><?php foreach($statuses as $s){ ?><option value="<?php echo $s; ?>"<?php echo $filter_status===$s?' selected="selected"':''; ?>><?php echo $s; ?></option><?php } ?></select></div>
          <div class="col-md-2"><label>Müşteri Tipi</label><select name="filter_customer_type" class="form-control"><option value="">Tümü</option><option value="Bireysel"<?php echo $filter_customer_type==='Bireysel'?' selected="selected"':''; ?>>Bireysel</option><option value="Kurumsal"<?php echo $filter_customer_type==='Kurumsal'?' selected="selected"':''; ?>>Kurumsal</option></select></div>
          <div class="col-md-2"><label>Yapı Türü</label><input type="text" name="filter_project_type" value="<?php echo htmlspecialchars($filter_project_type,ENT_QUOTES,'UTF-8'); ?>" class="form-control"></div>
          <div class="col-md-2" style="margin-top:12px"><label>Başlangıç</label><input type="date" name="filter_date_from" value="<?php echo htmlspecialchars($filter_date_from,ENT_QUOTES,'UTF-8'); ?>" class="form-control"></div>
          <div class="col-md-2" style="margin-top:12px"><label>Bitiş</label><input type="date" name="filter_date_to" value="<?php echo htmlspecialchars($filter_date_to,ENT_QUOTES,'UTF-8'); ?>" class="form-control"></div>
          <div class="col-md-4" style="margin-top:37px"><button class="btn btn-primary"><i class="fa fa-filter"></i> Filtrele</button> <a href="<?php echo $reset; ?>" class="btn btn-default">Temizle</a></div>
        </form>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-list"></i> Talepler</h3></div>
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead><tr>
            <th>ID</th><th>Tarih</th><th>Durum</th><th>Müşteri</th><th>Telefon</th><th>Yapı Türü</th><th>Lokasyon</th><th>m²</th><th>Mail</th><th class="text-right">İşlem</th>
          </tr></thead>
          <tbody>
          <?php if ($leads) { foreach ($leads as $lead) { ?>
            <tr>
              <td>#<?php echo (int)$lead['lead_id']; ?></td>
              <td><?php echo htmlspecialchars($lead['date_added'],ENT_QUOTES,'UTF-8'); ?></td>
              <td><span class="label <?php echo $lead['lead_status']==='Satış'?'label-success':($lead['lead_status']==='İptal'?'label-default':($lead['lead_status']==='Yeni'?'label-danger':'label-info')); ?>"><?php echo htmlspecialchars($lead['lead_status'],ENT_QUOTES,'UTF-8'); ?></span></td>
              <td><strong><?php echo htmlspecialchars($lead['name'],ENT_QUOTES,'UTF-8'); ?></strong><?php if($lead['company']){ ?><br><small><?php echo htmlspecialchars($lead['company'],ENT_QUOTES,'UTF-8'); ?></small><?php } ?><br><small><?php echo htmlspecialchars($lead['customer_type'],ENT_QUOTES,'UTF-8'); ?></small></td>
              <td><a href="tel:<?php echo htmlspecialchars($lead['phone'],ENT_QUOTES,'UTF-8'); ?>"><?php echo htmlspecialchars($lead['phone'],ENT_QUOTES,'UTF-8'); ?></a></td>
              <td><?php echo htmlspecialchars($lead['project_type'],ENT_QUOTES,'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($lead['location'],ENT_QUOTES,'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($lead['area'],ENT_QUOTES,'UTF-8'); ?></td>
              <td><?php echo $lead['mail_status']==='sent'?'<span class="label label-success">Gönderildi</span>':'<span class="label label-warning">'.htmlspecialchars($lead['mail_status'],ENT_QUOTES,'UTF-8').'</span>'; ?></td>
              <td class="text-right">
                <a href="<?php echo $lead['view']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>
                <a href="<?php echo $lead['delete']; ?>" onclick="return confirm('Bu teklif talebi silinsin mi?');" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
              </td>
            </tr>
          <?php } } else { ?>
            <tr><td colspan="10" class="text-center">Teklif talebi bulunamadı.</td></tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="panel-footer clearfix">
        <div class="pull-left"><?php echo $pagination; ?></div>
        <div class="pull-right"><?php echo $results; ?></div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
