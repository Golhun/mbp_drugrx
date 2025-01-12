<?php
// rss_fetcher.php - minimal partial fetch approach
header('Content-Type: application/json');
require_once 'config.php';
require_once 'rss_helpers.php';

$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$count = isset($_GET['count']) ? (int)$_GET['count'] : 10;

// Single or multiple feeds if you want
$feedUrls = 'https://www.drugs.com/feeds/medical_news.xml';
$items = fetchRssWithFallback($feedUrls);

$total = count($items);
$batch = array_slice($items, $start, $count);

echo json_encode([
    'items' => $batch,
    'total' => $total
]);
