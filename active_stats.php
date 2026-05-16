<?php
date_default_timezone_set('Africa/Cairo');
header('Content-Type: application/json; charset=UTF-8');

$file = 'data/active_stats_users.json';
$timeout = 60;
$clientId = $_GET['client_id'] ?? 'unknown';
$now = time();

if (!file_exists('data')) {
    mkdir('data', 0755, true);
}
if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}

$data = json_decode(file_get_contents($file), true);

foreach ($data as $key => $timestamp) {
    if ($now - $timestamp > $timeout) {
        unset($data[$key]);
    }
}

$data[$clientId] = $now;
file_put_contents($file, json_encode($data));

echo json_encode(['active_stats_viewers' => count($data)]);