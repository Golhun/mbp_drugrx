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
    if ($time_diff < 1) {
        echo json_encode(['error' => 'Too many requests. Please wait before retrying.']);
        exit;
    }
    $_SESSION['last_request_time'] = time();
}

// 📝 Parse JSON Input
$request = json_decode(file_get_contents('php://input'), true);
if (!$request || !isset($request['type'])) {
    echo json_encode(['error' => 'Invalid or malformed JSON request.']);
    exit;
}

// 🚀 Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mbp_drugrx";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    logError('Database Connection Failed: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to connect to the database.']);
    exit;
}

// 🟢 Handle Different Request Types
$type = $request['type'];

// 🟡 **1. Drug Suggestions Endpoint**
if ($type === 'suggestions') {
    $query = trim($request['query'] ?? '');

    if (empty($query) || strlen($query) < 2) {
        echo json_encode(['error' => 'Please enter at least 2 characters.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT DISTINCT drug1 
            FROM drug_interactions 
            WHERE drug1 LIKE :query 
            ORDER BY drug1 ASC 
            LIMIT 5
        ");
        $stmt->execute(['query' => "$query%"]);
        $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo json_encode(['suggestions' => $suggestions]);
    } catch (PDOException $e) {
        logError('Suggestion Fetch Failed: ' . $e->getMessage());
        echo json_encode(['error' => 'Failed to fetch suggestions.']);
    }
    exit;
}

// 🟡 **2. Drug Interaction Lookup (Database + API)**
elseif ($type === 'interactions') {
    $drugs = $request['drugs'] ?? [];
    if (!is_array($drugs) || count($drugs) < 1 || count($drugs) > 5) {
        echo json_encode(['error' => 'Please enter between 1 and 5 valid drug names.']);
        exit;
    }

    $dbResults = [];
    $apiResults = [];

    try {
        // 🟢 Database Lookup
        foreach ($drugs as $index => $drug1) {
            for ($j = $index + 1; $j < count($drugs); $j++) {
                $drug2 = $drugs[$j];

                $stmt = $pdo->prepare("
                    SELECT drug1, drug2, interaction_description, interaction_severity
                    FROM drug_interactions
                    WHERE (drug1 = :drug1 AND drug2 = :drug2) 
                       OR (drug1 = :drug2 AND drug2 = :drug1)
                ");
                $stmt->execute(['drug1' => trim($drug1), 'drug2' => trim($drug2)]);

                $interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                logError("Executed query for $drug1 ↔ $drug2, Found: " . count($interactions));

                foreach ($interactions as $interaction) {
                    $dbResults[] = [
                        'drug1' => $interaction['drug1'],
                        'drug2' => $interaction['drug2'],
                        'interaction_description' => $interaction['interaction_description'] ?? 'N/A',
                        'interaction_severity' => $interaction['interaction_severity'] ?? 'Unknown'
                    ];
                }
            }
        }

        if (empty($dbResults)) {
            logError("No database interactions found for selected drugs: " . implode(', ', $drugs));
        }

        // 🟢 API Lookup
        foreach ($drugs as $drug) {
            $url = "https://api.fda.gov/drug/label.json?search=active_ingredient:\"$drug\"&limit=1";
            try {
                $apiData = fetchFromOpenFDA($url);
                $apiResults[] = [
                    'drug' => $drug,
                    'warnings' => $apiData['warnings'][0] ?? 'No warnings available',
                    'interactions' => $apiData['drug_interactions'][0] ?? 'No interaction data available',
                    'description' => $apiData['description'] ?? 'No description available'
                ];
            } catch (Exception $e) {
                logError("OpenFDA Fetch Failed for $drug: " . $e->getMessage());
            }
        }

        echo json_encode([
            'db_results' => $dbResults,
            'api_results' => $apiResults
        ]);
    } catch (PDOException $e) {
        logError('Interaction Lookup Failed: ' . $e->getMessage());
        echo json_encode(['error' => 'Failed to fetch drug interactions.']);
    }
    exit;
}

// 🟡 **3. Fetch Data from OpenFDA API**
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

// 🛡️ **4. Error Logging**
function logError($message) {
    $logFile = __DIR__ . '/error.log';
    $error = "[" . date('Y-m-d H:i:s') . "] Error: $message\n";
    file_put_contents($logFile, $error, FILE_APPEND);
}
