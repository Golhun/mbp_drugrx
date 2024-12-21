<?php

$url = 'https://api.fda.gov/drug/label.json?limit=5';

$response = file_get_contents($url);
$data = json_decode($response, true);

echo '<pre>';
print_r($data);
echo '</pre>';

