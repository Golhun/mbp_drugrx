<?php
// Increase execution time limit
set_time_limit(600);

// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mbp_drugrx";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// CSV File
$csvFile = __DIR__ . '/drug_interactions.csv';

// Validate File
if (!file_exists($csvFile)) {
    die("❌ CSV file not found at $csvFile");
}

// Read CSV File and Insert in Batches
$batchSize = 1000; // Adjust batch size based on your server capacity
$batch = [];
$rowCount = 0;
$errorCount = 0;

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    fgetcsv($handle); // Skip the header row
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $drug1 = $conn->real_escape_string(trim($data[0]));
        $drug2 = $conn->real_escape_string(trim($data[1]));
        $interaction_description = $conn->real_escape_string(trim($data[2]));
        $interaction_severity = $conn->real_escape_string(trim($data[3]));

        if (empty($drug1) || empty($drug2)) {
            $errorCount++;
            continue;
        }

        $batch[] = "('$drug1', '$drug2', '$interaction_description', '$interaction_severity')";
        
        // Insert batch into database
        if (count($batch) >= $batchSize) {
            $insertValues = implode(',', $batch);
            $sql = "INSERT INTO drug_interactions (drug1, drug2, interaction_description, interaction_severity) 
                    VALUES $insertValues 
                    ON DUPLICATE KEY UPDATE 
                        interaction_description = VALUES(interaction_description),
                        interaction_severity = VALUES(interaction_severity)";

            if ($conn->query($sql) === TRUE) {
                $rowCount += count($batch);
            } else {
                echo "❌ Error: " . $conn->error . "<br>";
                $errorCount += count($batch);
            }

            $batch = []; // Clear batch
        }
    }

    // Insert remaining batch
    if (count($batch) > 0) {
        $insertValues = implode(',', $batch);
        $sql = "INSERT INTO drug_interactions (drug1, drug2, interaction_description, interaction_severity) 
                VALUES $insertValues 
                ON DUPLICATE KEY UPDATE 
                    interaction_description = VALUES(interaction_description),
                    interaction_severity = VALUES(interaction_severity)";

        if ($conn->query($sql) === TRUE) {
            $rowCount += count($batch);
        } else {
            echo "❌ Error: " . $conn->error . "<br>";
            $errorCount += count($batch);
        }
    }

    fclose($handle);
    echo "<p>✅ $rowCount rows inserted/updated successfully.<br>";
    echo "⚠️ $errorCount rows had errors.</p>";
} else {
    die("❌ Failed to open CSV file.");
}

// Close Database Connection
$conn->close();
?>
