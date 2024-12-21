<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Interaction Checker</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex items-center justify-center">
    <div class="max-w-3xl w-full p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-6">
            <span class="material-icons align-middle">medication</span> Drug Interaction Checker
        </h1>

        <!-- Input Form -->
        <form id="drug-form" class="space-y-4">
            <div>
                <label for="drugs" class="block text-lg font-medium text-gray-700">
                    Enter 1 to 5 Drug Names (comma-separated):
                    <span class="material-icons text-gray-400 align-middle" title="Example: ibuprofen, aspirin">help_outline</span>
                </label>
                <textarea id="drugs" name="drugs" rows="3"
                          class="w-full mt-2 p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="E.g., ibuprofen, acetaminophen"></textarea>
            </div>
            <button type="button" id="submit-button"
                    class="w-full bg-blue-500 text-white py-3 rounded-md hover:bg-blue-600 flex items-center justify-center">
                <span class="material-icons mr-2">search</span> Check Interactions
            </button>
        </form>

        <!-- Skeleton Loading -->
        <div id="loading" class="hidden mt-6 space-y-4">
            <div class="animate-pulse bg-gray-300 h-16 w-full rounded-md"></div>
            <div class="animate-pulse bg-gray-300 h-16 w-full rounded-md"></div>
        </div>

        <!-- Results Section -->
        <div id="results" class="mt-6 space-y-4"></div>
    </div>

    <script src="script.js"></script>
</body>
</html>

