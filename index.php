<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Interaction Checker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        /* Suggestions Dropdown */
        .suggestions {
            max-height: 250px;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col items-center py-6">

    <!-- 🟦 Header Section -->
    <header class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-md py-4 mb-6">
        <h1 class="text-center text-3xl font-bold flex items-center justify-center gap-2">
            <span class="material-icons">medication</span>
            Drug Interaction Checker
        </h1>
    </header>

    <!-- 🟩 Main Content Section -->
    <main class="max-w-4xl w-full px-6 py-8 bg-white shadow-lg rounded-lg">
        
        <!-- 🔍 Search & Selection Section -->
        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-700">Search and Add Drugs</h2>
            
            <div class="relative mb-4">
                <label for="drug-search" class="block text-lg font-medium text-gray-700 mb-2">
                    Search and Add Drugs:
                </label>
                <input type="text" id="drug-search"
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Type a drug name...">
                <div id="suggestions" 
                     class="absolute bg-white border border-gray-300 rounded-md mt-1 w-full shadow-lg hidden overflow-y-auto suggestions z-50">
                </div>
            </div>

            <!-- Selected Drugs Display -->
            <div id="selected-drugs" class="flex flex-wrap gap-2 mb-4"></div>
            
            <!-- Check Interactions Button -->
            <button id="check-interactions"
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white py-3 rounded-md hover:from-blue-600 hover:to-blue-800 transition-transform transform hover:scale-105">
                <span class="material-icons align-middle">search</span> Check Interactions
            </button>
        </section>

        <!-- ⏳ Loading State -->
        <section id="loading" class="hidden mt-6">
            <div class="flex justify-center items-center space-x-2">
                <div class="h-6 w-6 rounded-full bg-blue-400 animate-ping"></div>
                <div class="h-6 w-6 rounded-full bg-blue-600 animate-ping"></div>
                <div class="h-6 w-6 rounded-full bg-blue-800 animate-ping"></div>
            </div>
            <p class="text-center text-gray-600 mt-2">Fetching interactions... Please wait.</p>
        </section>

        <!-- 🟨 Results Section -->
        <section id="results" class="mt-6 space-y-6">
            <!-- Interaction Cards will be dynamically added here by JavaScript -->
        </section>

        <!-- 🛑 Empty State -->
        <section id="empty-state" class="hidden mt-12 text-center">
            <img src="https://via.placeholder.com/200" alt="No Results" class="mx-auto mb-4">
            <h3 class="text-xl font-semibold text-gray-700">No Results Found</h3>
            <p class="text-gray-600">Try entering different drug names or check your spelling.</p>
        </section>
    </main>

    <!-- 🟫 Footer Section -->
    <footer class="w-full mt-auto text-center py-4 bg-gray-100 text-gray-500 text-sm">
        &copy; <?php echo date("Y"); ?> Drug Interaction Checker. All rights reserved.
    </footer>

    <!-- 🟨 JavaScript File -->
    <script src="script.js"></script>
</body>
</html>
