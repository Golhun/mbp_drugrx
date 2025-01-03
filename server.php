<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

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

// Load Environment Variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 🚀 Database Connection
try {
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    logError('Database Connection Failed: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to connect to the database.']);
    exit;
}

// 📝 Parse JSON Input
$request = json_decode(file_get_contents('php://input'), true);
if (!$request || !isset($request['type'])) {
    echo json_encode(['error' => 'Invalid or malformed JSON request.']);
    exit;
}

$type = $request['type'];

// 🟢 Route Requests to Handlers
if ($type === 'suggestions') {
    handleSuggestions($pdo, $request);
} elseif ($type === 'interactions') {
    handleInteractions($pdo, $request);
} elseif ($type === 'substitutes') {
    handleSubstitutes($pdo, $request);
} else {
    echo json_encode(['error' => 'Invalid request type.']);
    exit;
}

// 🟢 **Suggestions Handler**
function handleSuggestions($pdo, $request) {
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
}

// 🟢 **Interactions Handler**
function handleInteractions($pdo, $request) {
    $drugs = $request['drugs'] ?? [];
    if (!is_array($drugs) || count($drugs) < 1 || count($drugs) > 5) {
        echo json_encode(['error' => 'Please enter between 1 and 5 valid drug names.']);
        exit;
    }

    try {
        $dbResults = [];
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
                $dbResults = array_merge($dbResults, $stmt->fetchAll(PDO::FETCH_ASSOC));
            }
        }

        echo json_encode(['db_results' => $dbResults]);
    } catch (PDOException $e) {
        logError('Interaction Lookup Failed: ' . $e->getMessage());
        echo json_encode(['error' => 'Failed to fetch drug interactions.']);
    }
}

// 🟢 **Substitutes Handler**
function handleSubstitutes($pdo, $request) {
    $query = trim($request['query'] ?? '');

    if (empty($query) || strlen($query) < 2) {
        echo json_encode(['error' => 'Please enter at least 2 characters.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT name, substitute0, substitute1, substitute2, substitute3, substitute4
            FROM drug_sub
            WHERE name LIKE :query
            LIMIT 5
        ");
        $stmt->execute(['query' => "$query%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $substitutes = [];
        foreach ($results as $row) {
            foreach (range(0, 4) as $i) {
                if (!empty($row["substitute$i"])) {
                    $substitutes[] = $row["substitute$i"];
                }
            }
        }

        echo json_encode(['substitutes' => array_unique($substitutes)]);
    } catch (PDOException $e) {
        logError('Substitute Lookup Failed: ' . $e->getMessage());
        echo json_encode(['error' => 'Failed to fetch substitutes.']);
    }
}

// 🛡️ **Error Logging**
function logError($message) {
    $logFile = __DIR__ . '/error.log';
    $error = "[" . date('Y-m-d H:i:s') . "] Error: $message\n";
    file_put_contents($logFile, $error, FILE_APPEND);
}
