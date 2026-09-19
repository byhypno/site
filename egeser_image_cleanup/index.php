<?php
/* Egeser Prefabrik güvenli görsel temizleme aracı. */
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: default-src 'self'; style-src 'unsafe-inline'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

define('EGESER_CLEANUP_TOKEN', '2d0c636f59e406a53426bdd7a821a9f448ec2461109db0366358292d53475f2a');
define('EGESER_EXPECTED_FILES', 155);
define('EGESER_CONFIRM_PHRASE', 'EGESER-155-DOSYA');

function egeser_escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function egeser_hash_equals($known, $given) {
    if (function_exists('hash_equals')) {
        return hash_equals($known, $given);
    }
    if (!is_string($known) || !is_string($given) || strlen($known) !== strlen($given)) {
        return false;
    }
    $result = 0;
    for ($i = 0; $i < strlen($known); $i++) {
        $result |= ord($known[$i]) ^ ord($given[$i]);
    }
    return $result === 0;
}

function egeser_fail($message, $status) {
    http_response_code($status);
    echo '<!doctype html><meta charset="utf-8"><title>İşlem durduruldu</title>';
    echo '<div style="font:16px Arial;max-width:760px;margin:50px auto;padding:24px;border:1px solid #ddd">';
    echo '<h1 style="color:#d71920">İşlem durduruldu</h1><p>' . egeser_escape($message) . '</p></div>';
    exit;
}

function egeser_valid_relative_path($path) {
    if (!is_string($path) || strpos($path, "\0") !== false || strpos($path, '\\') !== false) {
        return false;
    }
    if (strpos($path, 'catalog/') !== 0 || substr($path, 0, 1) === '/') {
        return false;
    }
    $parts = explode('/', $path);
    foreach ($parts as $part) {
        if ($part === '' || $part === '.' || $part === '..') {
            return false;
        }
    }
    return true;
}

function egeser_preflight($manifest, $imageRoot) {
    $result = array('matching' => array(), 'missing' => array(), 'changed' => array(), 'unsafe' => array());
    $rootReal = realpath($imageRoot);
    if ($rootReal === false || !is_dir($rootReal)) {
        $result['unsafe'][] = array('path' => '(image root)', 'reason' => 'image klasörü bulunamadı');
        return $result;
    }
    $prefix = rtrim($rootReal, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    foreach ($manifest['files'] as $item) {
        $relative = isset($item['path']) ? $item['path'] : '';
        if (!egeser_valid_relative_path($relative)) {
            $result['unsafe'][] = array('path' => $relative, 'reason' => 'geçersiz göreli yol');
            continue;
        }
        $target = $imageRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        if (!file_exists($target)) {
            $result['missing'][] = array('path' => $relative, 'reason' => 'dosya bulunamadı');
            continue;
        }
        if (is_link($target) || !is_file($target)) {
            $result['unsafe'][] = array('path' => $relative, 'reason' => 'sembolik bağlantı veya normal dosya değil');
            continue;
        }
        $targetReal = realpath($target);
        if ($targetReal === false || strpos($targetReal, $prefix) !== 0) {
            $result['unsafe'][] = array('path' => $relative, 'reason' => 'image kökü dışına çıkıyor');
            continue;
        }
        $actualSize = filesize($targetReal);
        $actualHash = hash_file('sha256', $targetReal);
        if ((int)$actualSize !== (int)$item['size_bytes'] || !egeser_hash_equals(strtolower($item['sha256']), strtolower($actualHash))) {
            $result['changed'][] = array('path' => $relative, 'reason' => 'boyut veya SHA-256 arşivle eşleşmiyor');
            continue;
        }
        $item['absolute_path'] = $targetReal;
        $result['matching'][] = $item;
    }
    return $result;
}

$providedToken = isset($_REQUEST['token']) ? (string)$_REQUEST['token'] : '';
if (!egeser_hash_equals(EGESER_CLEANUP_TOKEN, $providedToken)) {
    egeser_fail('Geçersiz veya eksik güvenlik anahtarı.', 404);
}

$packageDir = __DIR__;
$siteRoot = dirname($packageDir);
$imageRoot = $siteRoot . DIRECTORY_SEPARATOR . 'image';
$manifestPath = $packageDir . DIRECTORY_SEPARATOR . 'silme_manifesti.json';
if (!is_file($manifestPath)) {
    egeser_fail('silme_manifesti.json bulunamadı.', 500);
}
$manifest = json_decode(file_get_contents($manifestPath), true);
if (!is_array($manifest) || !isset($manifest['files']) || count($manifest['files']) !== EGESER_EXPECTED_FILES) {
    egeser_fail('Manifest geçersiz veya beklenen dosya sayısıyla eşleşmiyor.', 500);
}

$preflight = egeser_preflight($manifest, $imageRoot);
$isPost = isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
$resultMessage = '';
$backupPath = '';
$logPath = '';
$deleted = array();
$deleteErrors = array();

if ($isPost) {
    $confirm = isset($_POST['confirm_phrase']) ? trim((string)$_POST['confirm_phrase']) : '';
    if ($confirm !== EGESER_CONFIRM_PHRASE) {
        egeser_fail('Onay ifadesi hatalı. Silme yapılmadı.', 400);
    }
    if (!empty($preflight['unsafe'])) {
        egeser_fail('Güvensiz dosya yolu tespit edildi. Silme yapılmadı.', 400);
    }
    if (empty($preflight['matching'])) {
        egeser_fail('Silinebilir ve doğrulanmış dosya bulunamadı.', 400);
    }
    if (!class_exists('ZipArchive')) {
        egeser_fail('Sunucuda ZipArchive etkin değil. Yedek oluşturulamadığı için silme yapılmadı.', 500);
    }
    $backupDirCandidates = array(
        $siteRoot . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'download',
        $siteRoot . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'upload'
    );
    $backupDir = '';
    foreach ($backupDirCandidates as $candidate) {
        if (is_dir($candidate) && is_writable($candidate)) {
            $backupDir = $candidate;
            break;
        }
    }
    if ($backupDir === '') {
        egeser_fail('Yazılabilir güvenli yedek klasörü bulunamadı. Silme yapılmadı.', 500);
    }
    $timestamp = date('Ymd-His');
    $backupPath = $backupDir . DIRECTORY_SEPARATOR . 'egeser-gorsel-temizlik-yedegi-' . $timestamp . '.zip';
    $logPath = $siteRoot . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'egeser-gorsel-temizlik-' . $timestamp . '.log';
    $zip = new ZipArchive();
    if ($zip->open($backupPath, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
        egeser_fail('Yedek ZIP oluşturulamadı. Silme yapılmadı.', 500);
    }
    foreach ($preflight['matching'] as $item) {
        if (!$zip->addFile($item['absolute_path'], 'image/' . $item['path'])) {
            $zip->close();
            @unlink($backupPath);
            egeser_fail('Yedek ZIP içine dosya eklenemedi. Silme yapılmadı.', 500);
        }
    }
    if (!$zip->close() || !is_file($backupPath) || filesize($backupPath) < 1) {
        @unlink($backupPath);
        egeser_fail('Yedek ZIP doğrulanamadı. Silme yapılmadı.', 500);
    }
    foreach ($preflight['matching'] as $item) {
        if (@unlink($item['absolute_path'])) {
            $deleted[] = $item['path'];
        } else {
            $deleteErrors[] = $item['path'];
        }
    }
    $log = array(
        'time' => date('c'),
        'backup' => $backupPath,
        'deleted_count' => count($deleted),
        'failed_count' => count($deleteErrors),
        'missing_before_run' => $preflight['missing'],
        'changed_before_run' => $preflight['changed'],
        'deleted' => $deleted,
        'delete_errors' => $deleteErrors
    );
    @file_put_contents($logPath, json_encode($log, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    $resultMessage = count($deleted) . ' dosya silindi; ' . count($deleteErrors) . ' dosya silinemedi.';
    $preflight = egeser_preflight($manifest, $imageRoot);
}

$matchingCount = count($preflight['matching']);
$missingCount = count($preflight['missing']);
$changedCount = count($preflight['changed']);
$unsafeCount = count($preflight['unsafe']);
$matchingBytes = 0;
foreach ($preflight['matching'] as $item) {
    $matchingBytes += (int)$item['size_bytes'];
}
?><!doctype html>
<html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Egeser Görsel Temizleme</title>
<style>
body{margin:0;background:#f7f5f2;color:#171717;font:15px/1.5 Arial,sans-serif}.bar{background:#d71920;color:#fff;padding:20px}.wrap{max-width:980px;margin:24px auto;padding:0 18px}.card{background:#fff;border:1px solid #e5e5e5;border-radius:10px;padding:20px;margin-bottom:18px}.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.metric{background:#f7f5f2;border-radius:8px;padding:14px}.metric b{display:block;font-size:24px;color:#d71920}.warn{background:#fff1d6;border-left:5px solid #f4a126;padding:13px}.ok{background:#e2f0d9;border-left:5px solid #2e7d32;padding:13px}.bad{background:#fce8e8;border-left:5px solid #b9151b;padding:13px}input[type=text]{width:100%;box-sizing:border-box;padding:12px;border:1px solid #bbb;border-radius:7px}.btn{background:#d71920;color:#fff;border:0;border-radius:8px;padding:13px 20px;font-weight:bold;cursor:pointer}.btn:disabled{background:#aaa}code{background:#f1f1f1;padding:2px 5px;border-radius:3px}details{margin-top:10px}li{word-break:break-word}@media(max-width:700px){.grid{grid-template-columns:1fr 1fr}}
</style></head><body>
<div class="bar"><div class="wrap"><h1>Egeser Güvenli Görsel Temizleme</h1><div>Ön kontrol + zorunlu yedek + SHA-256 doğrulamalı silme</div></div></div>
<main class="wrap">
<?php if ($resultMessage !== ''): ?><div class="card ok"><strong><?php echo egeser_escape($resultMessage); ?></strong><br>Yedek: <code><?php echo egeser_escape($backupPath); ?></code><br>Log: <code><?php echo egeser_escape($logPath); ?></code></div><?php endif; ?>
<div class="card"><h2>Ön kontrol sonucu</h2><div class="grid">
<div class="metric"><b><?php echo $matchingCount; ?></b>Eşleşen</div>
<div class="metric"><b><?php echo $missingCount; ?></b>Eksik</div>
<div class="metric"><b><?php echo $changedCount; ?></b>Değişmiş</div>
<div class="metric"><b><?php echo $unsafeCount; ?></b>Güvensiz</div>
</div><p>Silinmeye uygun eşleşen boyut: <strong><?php echo number_format($matchingBytes / 1048576, 2, ',', '.'); ?> MB</strong></p></div>
<div class="card warn"><strong>Güvenlik:</strong> Sadece manifestte bulunan ve SHA-256 özeti birebir eşleşen dosyalar işleme alınır. Eksik veya değişmiş dosyalar silinmez. Yedek ZIP oluşturulamazsa işlem başlamaz.</div>
<?php if (!$isPost): ?>
<div class="card"><h2>Silme onayı</h2><p>Devam etmek için aşağıdaki kutuya <code><?php echo EGESER_CONFIRM_PHRASE; ?></code> yazın.</p>
<form method="post"><input type="hidden" name="token" value="<?php echo egeser_escape(EGESER_CLEANUP_TOKEN); ?>"><input type="text" name="confirm_phrase" autocomplete="off" required><p><button class="btn" type="submit" <?php echo $unsafeCount ? 'disabled' : ''; ?>>Yedek al ve eşleşen dosyaları sil</button></p></form></div>
<?php endif; ?>
<?php foreach (array('changed'=>'Değişmiş - silinmeyecek','missing'=>'Eksik','unsafe'=>'Güvensiz') as $key=>$title): if (!empty($preflight[$key])): ?>
<div class="card"><details><summary><strong><?php echo egeser_escape($title); ?> (<?php echo count($preflight[$key]); ?>)</strong></summary><ul><?php foreach ($preflight[$key] as $item): ?><li><?php echo egeser_escape($item['path'] . ' - ' . $item['reason']); ?></li><?php endforeach; ?></ul></details></div>
<?php endif; endforeach; ?>
<div class="card bad"><strong>İşlem sonrasında:</strong> Siteyi ve ürün görsellerini kontrol edin. Sorun yoksa bu <code>egeser_image_cleanup</code> klasörünü public_html içinden silin. Sorun varsa oluşturulan yedek ZIP'i public_html içine geri açın.</div>
</main></body></html>
