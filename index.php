<?php
require __DIR__ . '/vendor/autoload.php';

use MBPDrugRX\DrugInteractionChecker;

$checker = new DrugInteractionChecker();
$drugInfo = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $drugName = $_POST['drugName'] ?? '';
    if ($drugName) {
        $drugInfo = $checker->getDrugInfo($drugName);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MBP DrugRX</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4 text-center">MBP DrugRX Checker</h1>
        <form method="POST" class="bg-white shadow rounded-lg p-4">
            <div class="mb-4">
                <label for="drugName" class="block text-gray-700 font-bold mb-2">Drug Name</label>
                <input type="text" id="drugName" name="drugName" 
                    class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Enter the drug name" required>
            </div>
            <button type="submit" 
                class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">
                <span class="material-icons align-middle">search</span> Check Interactions
            </button>
        </form>

        <?php if ($drugInfo): ?>
            <div class="mt-6 bg-white shadow rounded-lg p-4">
                <h2 class="text-xl font-bold mb-4">Results</h2>
                <ul class="space-y-2">
                    <?php foreach ($drugInfo as $result): ?>
                        <li class="border-b pb-2">
                            <p><strong>Product:</strong> <?= htmlspecialchars($result['patient']['drug'][0]['medicinalproduct'] ?? 'Unknown') ?></p>
                            <p><strong>Reaction:</strong> <?= htmlspecialchars($result['patient']['reaction'][0]['reactionmeddrapt'] ?? 'Unknown') ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p class="mt-4 text-red-500">No results found. Please try another drug.</p>
        <?php endif; ?>
    </div>
</body>
</html>
