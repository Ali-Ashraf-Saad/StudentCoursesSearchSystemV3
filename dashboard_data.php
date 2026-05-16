<?php
date_default_timezone_set('Africa/Cairo');
header('Content-Type: application/json; charset=UTF-8');

if (!file_exists('data')) mkdir('data', 0755, true);

// عدادات الفتح
$statsFile = 'data/dashboard_stats_opens.txt';
$statsOpens = file_exists($statsFile) ? (int)file_get_contents($statsFile) : 0;

$pageFile = 'data/dashboard_page_opens.txt';
$pageOpens = file_exists($pageFile) ? (int)file_get_contents($pageFile) : 0;

$totalSearches = file_exists('counter.txt') ? (int)file_get_contents('counter.txt') : 0;

$exportFile = 'data/export_count.txt';
$exportCount = file_exists($exportFile) ? (int)file_get_contents($exportFile) : 0;

// عدادات الطلاب
$countsFile = 'data/search_counts.json';
$searchCounts = file_exists($countsFile) ? json_decode(file_get_contents($countsFile), true) : [];
arsort($searchCounts);

// أسماء الطلاب من البيانات الأصلية
$studentsData = file_exists('data/students.json') ? json_decode(file_get_contents('data/students.json'), true) : [];
$studentNames = [];
foreach ($studentsData as $s) {
    $studentNames[$s['id']] = $s['name'];
}

// بناء قائمة بجميع الطلاب مع الاسم
$allStudents = [];
foreach ($searchCounts as $id => $cnt) {
    $allStudents[] = [
        'id'    => $id,
        'name'  => $studentNames[$id] ?? 'غير معروف',
        'count' => $cnt
    ];
}
$topStudents = array_slice($allStudents, 0, 5);

// سجل البحث
$logFile = 'data/search_log.json';
$searchLog = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
$searchLog = array_slice(array_reverse($searchLog), 0, 50);

// المستخدمون النشطون (عادي + فاتحو الإحصائيات)
$activeFile = 'data/active_users.json';
$activeUsers = file_exists($activeFile) ? json_decode(file_get_contents($activeFile), true) : [];
$now = time();
$timeout = 60;
$activeCount = 0;
$statsActiveCount = 0;

foreach ($activeUsers as $id => $entry) {
    if (is_array($entry) && isset($entry['time'])) {
        if ($now - $entry['time'] <= $timeout) {
            $activeCount++;
            if (!empty($entry['stats'])) {
                $statsActiveCount++;
            }
        }
    } else {
        // بيانات قديمة (timestamp فقط)
        if ($now - $entry <= $timeout) {
            $activeCount++;
        }
    }
}

$lastSearch = !empty($searchLog) ? $searchLog[0]['time'] : 'لا يوجد';

// متوسط البحث في الساعة
$oneDayAgo = $now - 86400;
$recentSearches = array_filter($searchLog, function($entry) use ($oneDayAgo) {
    return strtotime($entry['time']) >= $oneDayAgo;
});
$avgPerHour = count($recentSearches) > 0 ? round(count($recentSearches) / 24, 2) : 0;

$uniqueStudents = count($allStudents);

$todayStart = strtotime('today');
$todaySearches = count(array_filter($searchLog, function($e) use ($todayStart) {
    return strtotime($e['time']) >= $todayStart;
}));

// ساعة الذروة
$hourly = [];
$weekAgo = $now - 604800;
foreach ($searchLog as $entry) {
    $t = strtotime($entry['time']);
    if ($t >= $weekAgo) {
        $hour = date('H', $t);
        $hourly[$hour] = ($hourly[$hour] ?? 0) + 1;
    }
}
$peakHour = !empty($hourly) ? array_search(max($hourly), $hourly) . ':00' : 'غير كافٍ';

// متوسط البحوث/مستخدم (باستخدام client_id)
$userSearches = [];
foreach ($searchLog as $entry) {
    $uid = $entry['client_id'] ?? ($entry['ip'] ?? 'unknown');
    $userSearches[$uid] = ($userSearches[$uid] ?? 0) + 1;
}
$avgPerUser = count($userSearches) > 0 ? round(array_sum($userSearches) / count($userSearches), 2) : 0;

echo json_encode([
    'all_students'       => $allStudents,
    'top_students'       => $topStudents,
    'search_log'         => $searchLog,
    'active_users'       => $activeCount,
    'stats_active_users' => $statsActiveCount,
    'stats_opens'        => $statsOpens,
    'page_opens'         => $pageOpens,
    'total_searches'     => $totalSearches,
    'last_search'        => $lastSearch,
    'avg_per_hour'       => $avgPerHour,
    'unique_students'    => $uniqueStudents,
    'today_searches'     => $todaySearches,
    'peak_hour'          => $peakHour,
    'avg_per_user'       => $avgPerUser,
    'export_count'       => $exportCount
], JSON_UNESCAPED_UNICODE);