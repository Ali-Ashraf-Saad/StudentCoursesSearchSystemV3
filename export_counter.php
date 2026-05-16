<?php
if (!file_exists('data')) mkdir('data', 0755, true);
$file = 'data/export_count.txt';
if (!file_exists($file)) file_put_contents($file, '0');
$count = (int)file_get_contents($file) + 1;
file_put_contents($file, $count);
echo json_encode(['export_count' => $count]);