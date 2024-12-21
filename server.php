<?php
header('Content-Type: application/json');

// Get JSON input
$request = json_decode(file_get_contents('php://input'), true);

if (!$request || !isset($request['drugs'])) {
    echo json_encode(['error' => 'Invalid input.']);
    exit;
}

// Parse drugs
$drugs = array_filter(array_map('trim', explode(',', $request['drugs'])));
if (count($drugs) < 1 || count($drugs) > 5) {
    echo json_encode(['error' => 'Enter between 1 and 5 drugs.']);
    exit;
}

// Fetch API data
echo json_encode(fetchDrugWarnings($drugs));
exit;

function fetchDrugWarnings(array $drugs) {
    $apiBase = 'https://api.fda.gov/drug/label.json';
    $data = [];

    foreach ($drugs as $drug) {
        $url = $apiBase . '?search=openfda.brand_name:"' . urlencode($drug) . '"&limit=1';
        try {
            $response = file_get_contents($url);
            $result = json_decode($response, true);

            if (isset($result['results'][0])) {
                $label = $result['results'][0];
                $data[$drug] = [
                    'warnings' => $label['warnings'][0] ?? 'No warnings available',
                    'interactions' => $label['drug_interactions'][0] ?? 'No interaction data available',
                ];
            } else {
                $data[$drug] = ['warnings' => 'No data found.', 'interactions' => 'No data found.'];
            }
        } catch (Exception $e) {
            $data[$drug] = ['warnings' => 'Error fetching data.', 'interactions' => 'Error fetching data.'];
        }
    }

    return $data;
}

