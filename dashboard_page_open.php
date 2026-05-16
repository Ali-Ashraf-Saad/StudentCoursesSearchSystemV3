<?php
if (!file_exists('data')) mkdir('data', 0755, true);
$file = 'data/dashboard_page_opens.txt';
if (!file_exists($file)) file_put_contents($file, '0');
$count = (int)file_get_contents($file) + 1;
file_put_contents($file, $count);
echo json_encode(['page_opens' => $count]);