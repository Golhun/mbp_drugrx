<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Array of drugs (using names) to check for interactions
$drugs = [
    'Aspirin',
    'Warfarin',
    'Ibuprofen',
    'Metformin',
    'Insulin'
];

// Function to check interactions for drug combinations using OpenFDA
function checkDrugInteractions($drugCombos) {
    $baseUrl = 'https://api.fda.gov/drug/label.json';

    foreach ($drugCombos as $combo) {
        echo "\nChecking interaction for combination: " . implode(', ', $combo) . "\n";

        $interactionFound = false;

        foreach ($combo as $drugName) {
            // URL encode drug name for the API query
            $drugNameEncoded = urlencode($drugName);

            // Construct the OpenFDA URL to get drug label information
            $url = "$baseUrl?search=drug_interaction:\"$drugNameEncoded\"&limit=1";

            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            // Execute the request
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                echo "cURL Error: " . curl_error($ch) . "\n";
                curl_close($ch);
                continue;
            }

            // Get HTTP response code
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            echo "HTTP Response Code: $httpCode\n";

            // Close cURL
            curl_close($ch);

            // Decode the response
            $data = json_decode($response, true);

            // Check if interaction data is found
            if ($httpCode == 200 && isset($data['results']) && !empty($data['results'])) {
                echo "Drug label for '$drugName' found with interaction information.\n";

                // Check for drug interaction information
                $foundInteraction = false;
                foreach ($data['results'][0]['drug_interaction'] as $interaction) {
                    echo " - " . $interaction . "\n";
                    $foundInteraction = true;
                }

                if (!$foundInteraction) {
                    echo "No interactions found for '$drugName'.\n";
                }

                $interactionFound = true;
            } else {
                echo "No interaction data found for '$drugName'.\n";
            }
        }

        // If no interaction is found for the combination, output accordingly
        if (!$interactionFound) {
            echo "No interactions found for the combination of drugs.\n";
        }

        // Add a small delay to avoid rate limiting
        sleep(1);
    }
}

// Generate combinations of drugs to check interactions
$combinations = [];

// Generate all pair combinations of drugs
for ($i = 0; $i < count($drugs); $i++) {
    for ($j = $i + 1; $j < count($drugs); $j++) {
        $combinations[] = [$drugs[$i], $drugs[$j]];
    }
}

// Check interactions for the generated combinations
checkDrugInteractions($combinations);
?>
