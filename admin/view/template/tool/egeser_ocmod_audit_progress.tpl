<?php echo $header; ?><?php echo $column_left; ?>
<div id="content"><div class="page-header"><div class="container-fluid"><h1><i class="fa fa-stethoscope"></i> EGESER Tam OCMOD Denetimi</h1></div></div>
<div class="container-fluid">
  <div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
  <div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">İlerleme</h3></div><div class="panel-body">
    <div class="progress" style="height:28px"><div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" style="width:<?php echo (int)$percent; ?>%;line-height:28px"><?php echo (int)$percent; ?>%</div></div>
    <p><strong><?php echo (int)$processed; ?></strong> / <strong><?php echo (int)$total; ?></strong> aktif modifikasyon işlendi.</p>
    <p>Bu sekmeyi kapatmayın. Her adım ayrı çalıştırıldığı için PHP zaman aşımına takılmadan ilerler.</p>
    <?php if (!empty($audit['results'])) { $last = end($audit['results']); ?><hr><p><strong>Son işlem:</strong> <?php echo htmlspecialchars($last['name'], ENT_QUOTES, 'UTF-8'); ?> — <code><?php echo htmlspecialchars($last['decision'], ENT_QUOTES, 'UTF-8'); ?></code></p><?php } ?>
  </div></div>
</div></div>
<script>setTimeout(function(){ window.location.href=<?php echo json_encode($next_url); ?>; }, 700);</script>
<?php echo $footer; ?>
