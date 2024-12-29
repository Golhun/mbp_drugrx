<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Interaction Checker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        /* Custom Styles */
        .suggestions {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .suggestion-item {
            padding: 8px;
            cursor: pointer;
        }

        .suggestion-item:hover {
            background: #f1f1f1;
        }

        .bubble {
            display: inline-flex;
            align-items: center;
            background: #e2f3ff;
            border-radius: 9999px;
            padding: 4px 8px;
            margin: 4px;
            font-size: 14px;
        }

        .bubble button {
            margin-left: 8px;
            background: none;
            border: none;
            color: red;
            cursor: pointer;
        }

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
            <h2 class="text-2xl font-bold mb-4 text-gray-700">Search and Add Drugs</h2>
            
            <!-- Drug Search Input with Suggestions -->
            <div class="relative">
                <label for="drug-search" class="block text-lg font-medium text-gray-700 mb-2">
                    Search and Add Drugs:
                </label>
                <input type="text" id="drug-search"
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Type a drug name...">
                <div id="suggestions" class="suggestions hidden"></div>
            </div>

            <!-- Selected Drugs Display -->
            <div id="selected-drugs" class="mt-4 flex flex-wrap"></div>
            
            <!-- Check Interactions Button -->
            <button id="check-interactions"
                    class="w-full bg-blue-500 text-white py-3 mt-4 rounded-md hover:bg-blue-600 transition-colors">
                <span class="material-icons align-middle">search</span> Check Interactions
            </button>
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
        <section id="results" class="mt-6 space-y-4 scrollable-results">
            <p class="text-center text-gray-600">Your results will appear here.</p>
        </section>
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

    <!-- JavaScript File -->
    <script src="script.js"></script>
</body>
</html>
