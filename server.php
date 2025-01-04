<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

header('Content-Type: application/json');

// Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Rate Limiting
session_start();
if (!isset($_SESSION['last_request_time'])) {
    $_SESSION['last_request_time'] = microtime(true);
} else {
    $time_diff = microtime(true) - $_SESSION['last_request_time'];
    if ($time_diff < 0.5) {
        sendResponse(['error' => 'Too many requests. Please wait before retrying.']);
        exit;
    }
    $_SESSION['last_request_time'] = microtime(true);
}

// Load Environment Variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database Connection
try {
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    logError('Database Connection Failed: ' . $e->getMessage());
    sendResponse(['error' => 'Failed to connect to the database.']);
    exit;
}

// Parse JSON Input
$request = json_decode(file_get_contents('php://input'), true);
if (!$request || !isset($request['type'])) {
    sendResponse(['error' => 'Invalid or malformed JSON request.']);
    exit;
}

$type = $request['type'];

// Route Requests to Handlers
switch ($type) {
    case 'suggestions':
        handleSuggestions($pdo, $request);
        break;
    case 'interactions':
        handleInteractions($pdo, $request);
        break;
    case 'substitutes':
        handleSubstitutes($pdo, $request);
        break;
    default:
        sendResponse(['error' => 'Invalid request type.']);
        break;
}

// Suggestions Handler
function handleSuggestions($pdo, $request) {
    $query = trim($request['query'] ?? '');

    if (empty($query) || strlen($query) < 2) {
        sendResponse(['error' => 'Please enter at least 2 characters.']);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT DISTINCT drug1 AS drug 
            FROM drug_interactions 
            WHERE drug1 LIKE :query
            UNION
            SELECT DISTINCT drug2 AS drug
            FROM drug_interactions 
            WHERE drug2 LIKE :query
            ORDER BY drug ASC
            LIMIT 10
        ");
        $stmt->execute(['query' => "%$query%"]);
        $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        sendResponse(['suggestions' => $suggestions]);
    } catch (PDOException $e) {
        logError('Suggestion Fetch Failed: ' . $e->getMessage());
        sendResponse(['error' => 'Failed to fetch suggestions.']);
    }
}

// Interactions Handler (Updated)
function handleInteractions($pdo, $request) {
    $drugs = $request['drugs'] ?? [];
    if (!is_array($drugs) || count($drugs) < 1 || count($drugs) > 5) {
        sendResponse(['error' => 'Please enter between 1 and 5 valid drug names.']);
        return;
    }

    $dbResults = [];
    $apiResults = [];

    try {
        // Database Lookup
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

        sendResponse([
            'db_results' => $dbResults,
            'api_results' => $apiResults, // Placeholder for future API results
        ]);
    } catch (PDOException $e) {
        logError('Interaction Lookup Failed: ' . $e->getMessage());
        sendResponse(['error' => 'Failed to fetch drug interactions.']);
    }
}

// Substitutes Handler
function handleSubstitutes($pdo, $request) {
    $query = trim($request['query'] ?? '');
    $selectedDrugs = $request['selectedDrugs'] ?? [];

    if (!empty($query)) { // Real-time suggestions
        if (strlen($query) < 2) {
            sendResponse(['error' => 'Please enter at least 2 characters.']);
            return;
        }

        try {
            $stmt = $pdo->prepare("
                SELECT DISTINCT name 
                FROM drug_sub
                WHERE LOWER(name) LIKE CONCAT('%', LOWER(:query), '%')
                LIMIT 10
            ");
            $stmt->execute(['query' => $query]);
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

            sendResponse(['suggestions' => $results]);
        } catch (PDOException $e) {
            logError('Substitute Suggestions Fetch Failed: ' . $e->getMessage());
            sendResponse(['error' => 'Failed to fetch suggestions.']);
        }
    } elseif (!empty($selectedDrugs)) { // Lookup substitutes for selected drugs
        if (!is_array($selectedDrugs) || count($selectedDrugs) < 1 || count($selectedDrugs) > 10) {
            sendResponse(['error' => 'Please select between 1 and 10 drugs.']);
            return;
        }

        try {
            $substituteResults = [];
            foreach ($selectedDrugs as $drug) {
                $stmt = $pdo->prepare("
                    SELECT name, substitute0, substitute1, substitute2, substitute3, substitute4,
                           chemical_class, therapeutic_class, action_class, side_effects, uses
                    FROM drug_sub
                    WHERE name = :name
                ");
                $stmt->execute(['name' => $drug]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($results as $row) {
                    $substitutes = [];
                    foreach (range(0, 4) as $i) {
                        if (!empty($row["substitute$i"])) {
                            $substitutes[] = $row["substitute$i"];
                        }
                    }

                    $substituteResults[] = [
                        'name' => $row['name'],
                        'substitutes' => array_unique($substitutes),
                        'chemical_class' => $row['chemical_class'] ?? 'N/A',
                        'therapeutic_class' => $row['therapeutic_class'] ?? 'N/A',
                        'action_class' => $row['action_class'] ?? 'N/A',
                        'side_effects' => $row['side_effects'] ?? 'N/A',
                        'uses' => $row['uses'] ?? 'N/A',
                    ];
                }
            }

            sendResponse(['details' => $substituteResults]);
        } catch (PDOException $e) {
            logError('Substitute Lookup Failed: ' . $e->getMessage());
            sendResponse(['error' => 'Failed to fetch substitutes.']);
        }
    } else {
        sendResponse(['error' => 'Invalid request for substitutes.']);
    }
}



// Response Wrapper
function sendResponse($data) {
    echo json_encode($data);
    exit;
}

// Error Logging
function logError($message) {
    $logFile = __DIR__ . '/error.log';
    $error = "[" . date('Y-m-d H:i:s') . "] Error: $message\n";
    file_put_contents($logFile, $error, FILE_APPEND);
}
