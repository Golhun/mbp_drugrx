<?php
header('Content-Type: application/json');

// 🛡️ Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// 🚀 Rate Limiting
session_start();
if (!isset($_SESSION['last_request_time'])) {
    $_SESSION['last_request_time'] = time();
} else {
    $time_diff = time() - $_SESSION['last_request_time'];
    if ($time_diff < 2) {
        echo json_encode(['error' => 'Too many requests. Please wait before retrying.']);
        exit;
    }
    $_SESSION['last_request_time'] = time();
}

// 📝 Parse JSON Input
$request = json_decode(file_get_contents('php://input'), true);
if (!$request || !isset($request['drugs'])) {
    echo json_encode(['error' => 'Invalid or malformed JSON request.']);
    exit;
}

// 🟢 Validate Input
$drugs = array_filter(array_map('trim', explode(',', htmlspecialchars($request['drugs']))));
if (count($drugs) < 1 || count($drugs) > 5) {
    echo json_encode(['error' => 'Please enter between 1 and 5 valid drug names.']);
    exit;
}

// 🟢 Fetch Data from OpenFDA
try {
    $results = getDrugInfoFromOpenFDA($drugs);
    echo json_encode(['results' => $results]);
} catch (Exception $e) {
    logError($e->getMessage());
    echo json_encode(['error' => 'Failed to fetch drug data from OpenFDA.']);
}

// 🟡 Function: Fetch Drug Info
function getDrugInfoFromOpenFDA($drugs) {
    $results = [];

    foreach ($drugs as $drug) {
        $labelUrl = "https://api.fda.gov/drug/label.json?search=openfda.brand_name:\"$drug\"&limit=1";

        try {
            $labelData = fetchFromOpenFDA($labelUrl);
            $warnings = $labelData['warnings'][0] ?? 'No warnings available';
            $interactions = $labelData['drug_interactions'][0] ?? 'No interaction data available';
            $indications = $labelData['indications_and_usage'] ?? 'No indications available';
            $purpose = $labelData['purpose'] ?? 'No purpose available';
            $description = $labelData['description'] ?? 'No description available';

            $results[] = [
                'drug' => $drug,
                'warnings' => $warnings,
                'interactions' => $interactions,
                'indications_and_usage' => $indications,
                'purpose' => $purpose,
                'description' => $description,
            ];
        } catch (Exception $e) {
            $results[] = [
                'drug' => $drug,
                'error' => 'Failed to fetch data from OpenFDA API: ' . $e->getMessage()
            ];
            logError("Failed to fetch data for $drug: " . $e->getMessage());
        }
    }

    return $results;
}

// 🟡 Function: Fetch from OpenFDA API
function fetchFromOpenFDA($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }

    curl_close($ch);

    $json = json_decode($response, true);
    if (!$json || !isset($json['results'][0])) {
        throw new Exception('Invalid or empty response from OpenFDA.');
    }

    return $json['results'][0];
}

// 🟡 Function: Log Errors
function logError($message) {
    $logFile = __DIR__ . '/error.log';
    $error = "[" . date('Y-m-d H:i:s') . "] Error: $message\n";
    file_put_contents($logFile, $error, FILE_APPEND);
}
