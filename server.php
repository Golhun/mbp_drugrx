<?php
header('Content-Type: application/json');

// 🛡️ Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// 🚀 Enable error reporting in development only
// error_reporting(E_ALL); ini_set('display_errors', 1);

// 🔐 Rate Limiting (Basic Implementation)
session_start();
if (!isset($_SESSION['last_request_time'])) {
    $_SESSION['last_request_time'] = time();
} else {
    $time_diff = time() - $_SESSION['last_request_time'];
    if ($time_diff < 2) { // Allow one request every 2 seconds
        echo json_encode(['error' => 'Too many requests. Please wait before retrying.']);
        exit;
    }
    $_SESSION['last_request_time'] = time();
}

// 📝 Parse JSON input securely
$request = json_decode(file_get_contents('php://input'), true);

if (!$request || !isset($request['drugs'])) {
    echo json_encode(['error' => 'Invalid or malformed JSON request.']);
    exit;
}

// 🟢 **Sanitize and Validate Input**
if (!is_string($request['drugs'])) {
    echo json_encode(['error' => 'Invalid drug input format.']);
    exit;
}

// Sanitize drug inputs
$drugs = array_filter(array_map('trim', explode(',', htmlspecialchars($request['drugs']))));

if (count($drugs) < 1 || count($drugs) > 5) {
    echo json_encode(['error' => 'Please enter between 1 and 5 valid drug names.']);
    exit;
}

// 🟢 **Fetch Drug Information from OpenFDA API**
try {
    $results = getDrugInfoFromOpenFDA($drugs);
    echo json_encode(['results' => $results]);
} catch (Exception $e) {
    logError($e->getMessage());
    echo json_encode(['error' => 'Failed to fetch drug data from OpenFDA.']);
}

// 🟡 **Function: Get Drug Information from OpenFDA API**
function getDrugInfoFromOpenFDA($drugs) {
    $results = [];

    foreach ($drugs as $drug) {
        $url = "https://api.fda.gov/drug/label.json?search=openfda.brand_name:\"$drug\"&limit=1";

        try {
            $apiResponse = fetchFromOpenFDA($url);
            $warnings = $apiResponse['warnings'][0] ?? 'No warnings available';
            $interactions = $apiResponse['drug_interactions'][0] ?? 'No interaction data available';
            $indications = $apiResponse['indications_and_usage'] ?? 'No indications available';
            $purpose = $apiResponse['purpose'] ?? 'No purpose available';
            $description = $apiResponse['description'] ?? 'No description available';
            $reference = $apiResponse['reference'][0] ?? 'No reference available';
            $reference_text = $apiResponse['reference_text'] ?? 'No reference text available';

            $results[] = [
                'drug' => $drug,
                'warnings' => $warnings,
                'interactions' => $interactions,
                'indications_and_usage' => $indications,
                'purpose' => $purpose,
                'description' => $description,
                'reference' => $reference,
                'reference_text' => $reference_text,
            ];
        } catch (Exception $e) {
            // Add partial data with an error message for failed drugs
            $results[] = [
                'drug' => $drug,
                'error' => 'Failed to fetch data from OpenFDA API: ' . $e->getMessage()
            ];
            logError("Failed to fetch data for $drug: " . $e->getMessage());
        }
    }

    return $results;
}

// 🟡 **Function: Fetch Data from OpenFDA API (SSL Disabled in Dev Mode)**
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

// 🟡 **Function: Log Critical Errors**
function logError($message) {
    $logFile = __DIR__ . '/error.log';
    $error = "[" . date('Y-m-d H:i:s') . "] Error: $message\n";
    file_put_contents($logFile, $error, FILE_APPEND);
}
