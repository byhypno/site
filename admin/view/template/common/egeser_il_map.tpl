<link rel="stylesheet" href="view/stylesheet/egeser-il-map.css" />
<script src="view/javascript/egeser-il-map.js" defer></script>

<style><?php echo $il_style_css; ?></style>
<script type="application/json" id="<?php echo $map_counts_id; ?>"><?php echo $il_counts_json; ?></script>

<div class="btn-group btn-group-xs egeser-il-map-toggle" style="margin-bottom:8px;">
  <button type="button" class="btn btn-default" data-map-view="crop"><i class="fa fa-map-marker"></i> Ege Bölgesi</button>
  <button type="button" class="btn btn-default active" data-map-view="full"><i class="fa fa-flag"></i> Tüm Türkiye</button>
</div>

<?php if (empty($il_svg_raw)) { ?>
<div class="egeser-il-map-missing">
  <i class="fa fa-exclamation-triangle"></i> Harita dosyası bulunamadı: <code>admin/view/image/egeser/turkey-map.svg</code><br />
  <small>Bu dosyanın sunucuya doğru yüklendiğinden emin olun.</small>
</div>
<?php } else { ?>
<div class="egeser-il-map-wrap" data-counts-id="<?php echo $map_counts_id; ?>" data-crop-slugs="izmir,manisa,aydin,usak,balikesir,mugla">
  <?php echo $il_svg_raw; ?>
</div>
<?php } ?>

<div class="egeser-il-legend">
  <span>Az</span>
  <span class="swatch" style="background:#eef2f6;border:1px solid #ddd;"></span>
  <span class="swatch" style="background:#c7dcf0;"></span>
  <span class="swatch" style="background:#8fb8df;"></span>
  <span class="swatch" style="background:#5790c8;"></span>
  <span class="swatch" style="background:#2f6aa8;"></span>
  <span class="swatch" style="background:#163f66;"></span>
  <span>Çok</span>
</div>

<?php if (!empty($il_ranking)) { ?>
<div class="table-responsive" style="margin-top:12px;">
  <table class="table table-hover table-condensed">
    <thead><tr><th>İl</th><th class="text-right">Ziyaretçi</th></tr></thead>
    <tbody>
    <?php foreach ($il_ranking as $row) { ?>
      <tr<?php echo $row['is_service_area'] ? ' class="info"' : ''; ?>>
        <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?><?php if ($row['is_service_area']) { ?> <span class="label label-primary">Hizmet Bölgesi</span><?php } ?></td>
        <td class="text-right"><?php echo (int)$row['visitors']; ?></td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>
<?php } else { ?>
<p class="text-muted text-center" style="margin-top:12px;">Bu tarih aralığında il tespiti yapılmış ziyaretçi verisi yok.</p>
<?php } ?>

<p class="text-muted" style="margin-top:6px;font-size:10px;">Harita: Turkey-SVG-Map (MIT) · İl tespiti: DB-IP City Lite (CC BY 4.0)</p>
