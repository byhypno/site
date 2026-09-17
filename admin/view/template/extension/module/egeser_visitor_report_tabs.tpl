<ul class="nav nav-tabs" style="margin-bottom:15px;">
  <?php foreach ($tabs as $tab) { ?>
  <li class="<?php echo $tab['active'] ? 'active' : ''; ?>"><a href="<?php echo $tab['href']; ?>"><?php echo $tab['text']; ?></a></li>
  <?php } ?>
</ul>

<?php if (!in_array($active_tab, array('settings', 'live'))) { ?>
<form class="form-inline" method="get" action="index.php" style="margin-bottom:15px;">
  <input type="hidden" name="route" value="<?php echo $base_route; ?>">
  <input type="hidden" name="token" value="<?php echo $token; ?>">
  <div class="btn-group" role="group" style="margin-right:10px;">
    <a href="<?php echo $range_links['today']; ?>" class="btn btn-<?php echo $range=='today'?'primary':'default'; ?>">Bugün</a>
    <a href="<?php echo $range_links['yesterday']; ?>" class="btn btn-<?php echo $range=='yesterday'?'primary':'default'; ?>">Dün</a>
    <a href="<?php echo $range_links['7days']; ?>" class="btn btn-<?php echo $range=='7days'?'primary':'default'; ?>">Son 7 Gün</a>
    <a href="<?php echo $range_links['30days']; ?>" class="btn btn-<?php echo $range=='30days'?'primary':'default'; ?>">Son 30 Gün</a>
    <a href="<?php echo $range_links['month']; ?>" class="btn btn-<?php echo $range=='month'?'primary':'default'; ?>">Bu Ay</a>
  </div>
  <input type="hidden" name="range" value="custom">
  <input type="date" name="date_from" value="<?php echo $date_from; ?>" class="form-control" style="width:150px;display:inline-block;">
  <span>&ndash;</span>
  <input type="date" name="date_to" value="<?php echo $date_to; ?>" class="form-control" style="width:150px;display:inline-block;">
  <button type="submit" class="btn btn-default"><i class="fa fa-filter"></i> Filtrele</button>
  <?php if (isset($export_url)) { ?>
  <a href="<?php echo $export_url; ?>" class="btn btn-default pull-right"><i class="fa fa-download"></i> CSV İndir</a>
  <?php } ?>
</form>
<?php } ?>

<?php if (!empty($success)) { ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div><?php } ?>
