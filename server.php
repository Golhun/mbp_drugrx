<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'config.php';         // For $db (the PDO instance)
require_once 'log_activity.php';   // For logActivity, updateFavoriteDrug
// If you have a unified RSS logic file, include it, for example:
// require_once 'rss_helpers.php';

// ---------------------------------------------------------------------------
// 1) Fix for the "Undefined variable $pdo" error:
//    If your config.php provides $db, we just alias $pdo = $db.
if (!isset($db)) {
    // If for some reason config.php did not define $db, handle or exit
    die('Database connection ($db) not defined in config.php');
}
$pdo = $db;   // <-- The fix: Now $pdo references the same PDO object as $db
// ---------------------------------------------------------------------------

// JSON + Security Headers
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Rate Limiting
session_start();
if (!isset($_SESSION['last_request_time'])) {
    $_SESSION['last_request_time'] = microtime(true);
} else {
    $time_diff = microtime(true) - $_SESSION['last_request_time'];
    if ($time_diff < 0.1) {
        sendResponse(['error' => 'Too many requests. Please wait before retrying.']);
        exit;
    }
    $_SESSION['last_request_time'] = microtime(true);
}

/**
 * We parse JSON from the POST body, but also handle GET params (for "fetchRSSBatch" or others).
 */
$request = json_decode(file_get_contents('php://input'), true);

// We'll figure out $type from either $_GET['type'] or $request['type'].
$type = null;
if (isset($_GET['type'])) {
    $type = $_GET['type'];
} elseif ($request && isset($request['type'])) {
    $type = $request['type'];
}
if (!$type) {
    sendResponse(['error' => 'Invalid or malformed request (missing type).']);
    exit;
}

// Route requests
switch ($type) {
    case 'fetchRSSBatch':
        handleFetchRssBatch();
        break;
    case 'suggestions':
        handleSuggestions($pdo, $request);
        break;
    case 'interactions':
        handleInteractions($pdo, $request);
        break;
    case 'substitutes':
        handleSubstitutes($pdo, $request);
        break;
    case 'druginfo':
        handleDrugInfo($pdo, $request);
        break;
    default:
        sendResponse(['error' => 'Invalid request type.']);
        break;
}

/**
 * handleFetchRssBatch() - Called by blog.js with GET ?type=fetchRSSBatch&start=0&count=10
 */
function handleFetchRssBatch() {
    $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
    $count = isset($_GET['count']) ? (int)$_GET['count'] : 10;

    // Example feed:
    $rssUrl = 'https://www.drugs.com/feeds/medical_news.xml';

    // If you have a caching approach, call fetchRssWithFallback($rssUrl). Otherwise:
    $items = tryFetchRss($rssUrl);

    $total = count($items);
    $batch = array_slice($items, $start, $count);

    sendResponse([
        'items' => $batch,
        'total' => $total
    ]);
}

/**
 * Minimal direct approach to fetch & parse an RSS feed (no caching).
 */
function tryFetchRss($url) {
    $items = [];
    try {
        $xmlString = @file_get_contents($url);
        if (!$xmlString) {
            return [];
        }
        $xml = @simplexml_load_string($xmlString);
        if (!$xml || !isset($xml->channel->item)) {
            return [];
        }
        foreach ($xml->channel->item as $entry) {
            $items[] = [
                'title'       => (string)($entry->title ?? ''),
                'description' => (string)($entry->description ?? ''),
                'link'        => (string)($entry->link ?? ''),
                'pubDate'     => (string)($entry->pubDate ?? '')
            ];
        }
        // Optionally sort by pubDate desc
        usort($items, function($a, $b) {
            return strtotime($b['pubDate'] ?? '0') - strtotime($a['pubDate'] ?? '0');
        });
    } catch (\Exception $e) {
        logError('tryFetchRss error: ' . $e->getMessage());
    }
    return $items;
}

// =====================================================
// suggestions (drug search)
function handleSuggestions($pdo, $request) {
    if (!$request) $request = [];
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
        $stmt->execute([':query' => "%$query%"]);
        $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        sendResponse(['suggestions' => $suggestions]);
    } catch (PDOException $e) {
        logError('Suggestion Fetch Failed: ' . $e->getMessage());
        sendResponse(['error' => 'Failed to fetch suggestions.']);
    }
}

// =====================================================
// interactions handler
function handleInteractions($pdo, $request) {
    if (!$request) $request = [];
    $drugs = $request['drugs'] ?? [];
    if (!is_array($drugs) || count($drugs) < 1 || count($drugs) > 5) {
        sendResponse(['error' => 'Please enter between 1 and 5 valid drug names.']);
        return;
    }

    $dbResults = [];
    $apiResults = [];

    try {
        // Log the interaction check
        if (isset($_SESSION['user_id'])) {
            logActivity($pdo, $_SESSION['user_id'], 'check_interactions', json_encode(['drugs' => $drugs]));
            // Also update favorite drugs
            foreach ($drugs as $drug) {
                updateFavoriteDrug($pdo, $_SESSION['user_id'], $drug);
            }
        }

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
                $stmt->execute([
                    ':drug1' => trim($drug1),
                    ':drug2' => trim($drug2)
                ]);

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
            'api_results' => $apiResults
        ]);
    } catch (PDOException $e) {
        logError('Interaction Lookup Failed: ' . $e->getMessage());
        sendResponse(['error' => 'Failed to fetch drug interactions.']);
    }
}

// =====================================================
// substitutes handler
function handleSubstitutes($pdo, $request) {
    if (!$request) $request = [];
    $query = trim($request['query'] ?? '');
    $selectedDrugs = $request['selectedDrugs'] ?? [];

    if (!empty($query)) {
        // real-time suggestions
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
            $stmt->execute([':query' => $query]);
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

            sendResponse(['suggestions' => $results]);
        } catch (PDOException $e) {
            logError('Substitute Suggestions Fetch Failed: ' . $e->getMessage());
            sendResponse(['error' => 'Failed to fetch suggestions.']);
        }
    } elseif (!empty($selectedDrugs)) {
        // Lookup actual substitutes
        if (!is_array($selectedDrugs) || count($selectedDrugs) < 1 || count($selectedDrugs) > 10) {
            sendResponse(['error' => 'Please select between 1 and 10 drugs.']);
            return;
        }

        try {
            if (isset($_SESSION['user_id'])) {
                logActivity($pdo, $_SESSION['user_id'], 'check_substitutes', json_encode(['selectedDrugs' => $selectedDrugs]));
                // Also update favorites
                foreach ($selectedDrugs as $drug) {
                    updateFavoriteDrug($pdo, $_SESSION['user_id'], $drug);
                }
            }

            $substituteResults = [];
            foreach ($selectedDrugs as $drug) {
                $stmt = $pdo->prepare("
                    SELECT name, substitute0, substitute1, substitute2, substitute3, substitute4,
                           chemical_class, therapeutic_class, action_class, side_effects, uses
                    FROM drug_sub
                    WHERE name = :name
                ");
                $stmt->execute([':name' => $drug]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                    $subs = [];
                    foreach (range(0, 4) as $i) {
                        if (!empty($row["substitute$i"])) {
                            $subs[] = $row["substitute$i"];
                        }
                    }

                    $substituteResults[] = [
                        'name' => $row['name'],
                        'substitutes' => array_unique($subs),
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

// =====================================================
// drug info handler
function handleDrugInfo($pdo, $request) {
    if (!$request) $request = [];
    $drugName = trim($request['drugName'] ?? '');
    if (!$drugName) {
        sendResponse(['error' => 'No drugName provided.']);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT composition, uses, side_effects
            FROM drug_info
            WHERE composition LIKE CONCAT('%', :drugName, '%')
            LIMIT 1
        ");
        $stmt->execute([':drugName' => $drugName]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            sendResponse([
                'found' => true,
                'composition' => $row['composition'],
                'uses' => $row['uses'],
                'side_effects' => $row['side_effects'],
            ]);
        } else {
            sendResponse(['found' => false]);
        }
    } catch (PDOException $e) {
        logError('Drug Info Lookup Failed: ' . $e->getMessage());
        sendResponse(['error' => 'Failed to fetch drug info.']);
    }
}

// =====================================================
// Response + error log
function sendResponse($data) {
    echo json_encode($data);
    exit;
}

function logError($message) {
    $logFile = __DIR__ . '/error.log';
    $errorLine = "[" . date('Y-m-d H:i:s') . "] Error: $message\n";
    file_put_contents($logFile, $errorLine, FILE_APPEND);
}
