<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $cancel; ?>" class="btn btn-default"><?php echo $button_cancel; ?></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?><div class="alert alert-danger"><?php echo $error_warning; ?></div><?php } ?>
    <?php if ($success) { ?><div class="alert alert-success"><?php echo $success; ?></div><?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-picture-o"></i> Projeler / Referanslar İçerik Kurulumu</h3></div>
      <div class="panel-body">
        <p><?php echo $text_intro; ?></p>
        <?php if ($found) { ?>
        <div class="well">
          <strong>Bulunan kayıt:</strong> information_id=<?php echo $information_id; ?> — <?php echo $current_title; ?><br>
          <strong>Mevcut meta title:</strong> <?php echo $current_meta_title ? $current_meta_title : '-'; ?><br>
          <strong>Mevcut description:</strong> <?php echo $has_description ? 'Var' : 'Boş'; ?>
        </div>
        <form action="<?php echo $action; ?>" method="post">
          <div class="checkbox"><label><input type="checkbox" name="confirm" value="1"> <?php echo $entry_confirm; ?></label></div>
          <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i> <?php echo $button_apply; ?></button>
        </form>
        <?php } else { ?>
        <div class="alert alert-warning">Projelerimiz ve Referanslar sayfası bulunamadı. V10.1 Site Skeleton kaydını kontrol edin.</div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
