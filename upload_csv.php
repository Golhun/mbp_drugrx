<?php
// 🚀 Increase execution time & memory limit for large uploads
set_time_limit(0);
ini_set('memory_limit', '1024M');

// 📊 Database Configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mbp_drugrx";

// 🛡️ Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Enable Strict Mode for Error Detection
$conn->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES'");

// 📂 CSV File Path
$csvFile = __DIR__ . '/1_medicine_dataset_processed.csv';

// Validate File
if (!file_exists($csvFile)) {
    die("❌ CSV file not found at $csvFile");
}

// 🚀 Batch Parameters
$batchSize = 1000; // Adjust based on server capacity
$rowCount = 0;
$errorCount = 0;

// 🚀 Prepare Queries
// Check for Existing IDs
$checkSQL = "SELECT id FROM drug_sub WHERE id = ?";
$checkStmt = $conn->prepare($checkSQL);
if (!$checkStmt) {
    die("❌ Failed to prepare check query: " . $conn->error);
}
$checkStmt->bind_param("i", $id);

// Insert New Rows
$insertSQL = "
    INSERT INTO drug_sub (
        id, name, substitute0, substitute1, substitute2, substitute3, substitute4,
        chemical_class, therapeutic_class, action_class, side_effects, uses
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";
$insertStmt = $conn->prepare($insertSQL);
if (!$insertStmt) {
    die("❌ Failed to prepare insert query: " . $conn->error);
}
$insertStmt->bind_param(
    "isssssssssss",
    $id, $name, $sub0, $sub1, $sub2, $sub3, $sub4,
    $chemical_class, $therapeutic_class, $action_class, $side_effects, $uses
);

// 🚀 Read CSV and Process Data
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    fgetcsv($handle); // Skip header row
    
    $batch = 0;
    $conn->begin_transaction(); // Start Transaction

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // 📝 Map CSV columns to variables
        $id = intval($data[0]);
        $name = trim($data[1]);
        $sub0 = trim($data[2]);
        $sub1 = trim($data[3]);
        $sub2 = trim($data[4]);
        $sub3 = trim($data[5]);
        $sub4 = trim($data[6]);
        $chemical_class = trim($data[7]);
        $therapeutic_class = trim($data[8]);
        $action_class = trim($data[9]);
        $side_effects = trim($data[10]);
        $uses = trim($data[11]);

        // 🛡️ Validate Required Fields
        if (empty($id) || empty($name)) {
            error_log("⚠️ Invalid row: Missing ID or name (ID: $id, Name: $name)");
            $errorCount++;
            continue;
        }

        // 🔍 Check if ID Already Exists
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        if ($result->num_rows > 0) {
            error_log("⚠️ Duplicate ID skipped: $id");
            continue; // Skip duplicates
        }

        // 🚀 Insert Valid Data
        if (!$insertStmt->execute()) {
            error_log("❌ Error inserting row ID $id: " . $insertStmt->error);
            $errorCount++;
        } else {
            $rowCount++;
        }

        // 🚀 Commit Every N Rows
        $batch++;
        if ($batch >= $batchSize) {
            $conn->commit();
            $conn->begin_transaction(); // Start new transaction
            $batch = 0;
        }
    }

    // 🚀 Final Commit for Remaining Rows
    $conn->commit();

    fclose($handle);
    echo "<p>✅ $rowCount new rows inserted successfully.<br>";
    echo "⚠️ $errorCount rows had errors. Check error log for details.</p>";
} else {
    die("❌ Failed to open CSV file.");
}

// 🛡️ Cleanup
$checkStmt->close();
$insertStmt->close();
$conn->close();
?>
