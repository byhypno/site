<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><i class="fa fa-inbox"></i> <?php echo $heading_title; ?>
      <span class="label label-primary" style="margin-left:6px;">Bugün: <?php echo $today_count; ?></span>
      <span class="label label-default">7 gün: <?php echo $week_count; ?></span>
    </h3>
  </div>
  <div class="table-responsive">
    <table class="table table-hover" style="margin-bottom:0;">
      <tbody>
      <?php if ($leads) { foreach ($leads as $lead) { ?>
        <tr>
          <td style="white-space:nowrap;"><span class="label <?php echo $lead['status_class']; ?>"><?php echo htmlspecialchars($lead['lead_status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
          <td>
            <a href="<?php echo $lead['view_url']; ?>"><strong><?php echo htmlspecialchars($lead['name'], ENT_QUOTES, 'UTF-8'); ?></strong></a>
            <?php if ($lead['company']) { ?><br /><small class="text-muted"><?php echo htmlspecialchars($lead['company'], ENT_QUOTES, 'UTF-8'); ?></small><?php } ?>
          </td>
          <td><?php echo htmlspecialchars($lead['phone'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars($lead['product_name'] ?: $lead['project_type'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td class="text-right text-muted"><small><?php echo $lead['time_ago']; ?></small></td>
        </tr>
      <?php } } else { ?>
        <tr><td class="text-center text-muted" style="padding:16px;">Henüz teklif talebi yok.</td></tr>
      <?php } ?>
      </tbody>
    </table>
  </div>
  <div class="panel-footer text-right">
    <a href="<?php echo $list_url; ?>"><i class="fa fa-list"></i> Tüm Talepler &raquo;</a>
  </div>
</div>
