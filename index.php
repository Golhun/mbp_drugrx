<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Interaction Checker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        /* Additional custom styles */
        .scrollable-results {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col items-center py-6">
    <!-- Header Section -->
    <header class="w-full bg-blue-600 text-white shadow-md py-4 mb-6">
        <h1 class="text-center text-3xl font-bold">
            <span class="material-icons align-middle">medication</span>
            Drug Interaction Checker
        </h1>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl w-full px-4 md:px-6 py-6 bg-white shadow-lg rounded-lg">
        <!-- Input Section -->
        <section>
            <h2 class="text-2xl font-bold mb-4 text-gray-700">Check Drug Interactions</h2>
            <form id="drug-form" class="space-y-4">
                <label for="drugs" class="block text-lg font-medium text-gray-700">
                    Enter 1 to 5 Drug Names (comma-separated):
                </label>
                <textarea id="drugs" rows="3" 
                          class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="e.g., aspirin, ibuprofen, paracetamol"></textarea>
                <button type="button" id="submit-button"
                        class="w-full bg-blue-500 text-white py-3 rounded-md hover:bg-blue-600 transition-colors">
                    <span class="material-icons align-middle">search</span> Check Interactions
                </button>
            </form>
            <p id="input-error" class="text-red-500 text-sm mt-2 hidden">⚠️ Please enter valid drug names (1 to 5).</p>
        </section>

        <!-- Loading State -->
        <section id="loading" class="hidden mt-6">
            <div class="flex justify-center items-center space-x-2">
                <div class="h-6 w-6 rounded-full bg-blue-400 animate-ping"></div>
                <div class="h-6 w-6 rounded-full bg-blue-600 animate-ping"></div>
                <div class="h-6 w-6 rounded-full bg-blue-800 animate-ping"></div>
            </div>
            <p class="text-center text-gray-600 mt-2">Fetching interactions... Please wait.</p>
        </section>

        <!-- Results Section -->
        <section id="results" class="mt-6 space-y-4 scrollable-results"></section>
    </main>

    <!-- Empty State -->
    <section id="empty-state" class="hidden mt-12 text-center">
        <img src="https://via.placeholder.com/200" alt="No Results" class="mx-auto mb-4">
        <h3 class="text-xl font-semibold text-gray-700">No Results Found</h3>
        <p class="text-gray-600">Try entering different drug names or check your spelling.</p>
    </section>

    <!-- Footer Section -->
    <footer class="w-full mt-auto text-center py-4 bg-gray-100 text-gray-500 text-sm">
        &copy; <?php echo date("Y"); ?> Drug Interaction Checker. All rights reserved.
    </footer>

    <!-- Modal for Detailed Info -->
    <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative">
            <h3 id="modal-title" class="text-xl font-bold mb-4">Drug Details</h3>
            <div id="modal-content" class="text-gray-700 space-y-2"></div>
            <button id="modal-close"
                    class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                <span class="material-icons">close</span>
            </button>
        </div>
    </div>

    <!-- JavaScript File -->
    <script src="script.js"></script>
</body>
</html>
