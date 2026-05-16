<?php
date_default_timezone_set('Africa/Cairo');
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$dataDir = 'data';
$file    = $dataDir . '/active_users.json';
$timeout = 20; // ثواني قبل اعتبار المستخدم منتهي الجلسة (النبضة كل 10 ثوانٍ)
$now     = time();

// ───── تحديد هوية العميل ─────
$clientId = trim($_GET['client_id'] ?? '');
if ($clientId === '') {
    $clientId = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

// ───── قراءة معامل stats ─────
// null  → لم يُرسَل (نبضة عادية) → لا نغيّر قيمة stats الحالية
// '1'   → المستخدم فتح لوحة الإحصائيات
// '0'   → المستخدم أغلق لوحة الإحصائيات
$statsParam  = isset($_GET['stats']) ? $_GET['stats'] : null;
$statsUpdate = ($statsParam !== null);          // هل نحدّث الـ flag؟
$statsValue  = ($statsParam === '1');           // القيمة الجديدة إذا قرّرنا التحديث

// ───── تجهيز المجلد والملف ─────
if (!file_exists($dataDir)) {
    mkdir($dataDir, 0755, true);
}
if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}

// ───── قراءة / تعديل / كتابة مع قفل حصري لمنع race conditions ─────
$fp = fopen($file, 'c+');
if (!$fp) {
    echo json_encode(['active_count' => 0, 'stats_active_count' => 0]);
    exit;
}

flock($fp, LOCK_EX); // قفل حصري

// قراءة المحتوى
$raw  = '';
while (!feof($fp)) {
    $raw .= fread($fp, 8192);
}
$data = json_decode($raw, true);
if (!is_array($data)) {
    $data = [];
}

// ── 1. حذف المنتهية الصلاحية ──
foreach ($data as $key => $entry) {
    $entryTime = is_array($entry) ? ($entry['time'] ?? 0) : (int)$entry;
    if ($now - $entryTime > $timeout) {
        unset($data[$key]);
    }
}

// ── 2. تحديث / إضافة العميل الحالي ──
if (isset($data[$clientId]) && is_array($data[$clientId])) {
    // المستخدم موجود مسبقاً: حدّث الوقت وربما الـ stats flag
    $data[$clientId]['time'] = $now;
    if ($statsUpdate) {
        $data[$clientId]['stats'] = $statsValue;
    }
} else {
    // مستخدم جديد أو بيانات قديمة (timestamp فقط)
    $data[$clientId] = [
        'time'  => $now,
        'stats' => $statsUpdate ? $statsValue : false,
    ];
}

// ── 3. الكتابة ──
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

flock($fp, LOCK_UN); // فكّ القفل
fclose($fp);

// ───── حساب العدادات من البيانات المحدَّثة ─────
$activeCount      = 0;
$statsActiveCount = 0;

foreach ($data as $entry) {
    $entryTime = is_array($entry) ? ($entry['time'] ?? 0) : (int)$entry;
    if ($now - $entryTime <= $timeout) {
        $activeCount++;
        if (is_array($entry) && !empty($entry['stats'])) {
            $statsActiveCount++;
        }
    }
}

echo json_encode([
    'active_count'       => $activeCount,
    'stats_active_count' => $statsActiveCount,
]);