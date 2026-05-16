<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$files = [
    'counter.txt',
    'data/dashboard_stats_opens.txt',
    'data/dashboard_page_opens.txt',
    'data/search_counts.json',
    'data/search_log.json',
    'data/active_users.json',
    'data/export_count.txt'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        if (substr($file, -4) === '.txt') {
            file_put_contents($file, '0');
        } elseif (substr($file, -5) === '.json') {
            file_put_contents($file, '[]');
        }
    }
}

echo json_encode(['success' => true, 'message' => 'تم تصفير جميع الإحصائيات']);