<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Interaction Checker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        /* Smooth Transition Styles */
        #tab-content-container {
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .opacity-50 {
            opacity: 0;
        }
        .scale-95 {
            transform: scale(0.95);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col items-center py-6">

    <!-- Header Section -->
    <header class="w-full py-4 mb-6">
        <h1 class="text-center text-3xl font-bold flex items-center justify-center gap-2 text-black">
            <span class="material-icons">medication</span>
            Drug Checker
        </h1>
    </header>

    <!-- Main Content Section -->
    <main class="max-w-4xl w-full px-6 py-8 bg-white shadow-lg rounded-lg">

        <!-- Tab Navigation -->
        <div class="mb-6">
            <div class="flex border-b tabs">
                <button id="tab-interactions" 
                        class="px-4 py-2 text-gray-600 hover:text-blue-600 border-b-2 border-transparent transition active">
                    Drug Interactions
                </button>
                <button id="tab-substitutes" 
                        class="px-4 py-2 text-gray-600 hover:text-green-600 border-b-2 border-transparent transition">
                    Drug Substitutes
                </button>
            </div>
        </div>

        <!-- Dynamic Tab Content -->
        <div id="tab-content-container">
            <p class="text-gray-500 text-center">Loading...</p>
        </div>
    </main>

    <!-- Footer Section -->
    <footer class="w-full mt-auto py-4 bg-gray-100 text-center text-gray-500 text-sm">
        &copy; <?php echo date("Y"); ?> Drug Checker. All rights reserved.
    </footer>

    <!-- JavaScript -->
    <script type="module" src="js/script.js"></script>
</body>
</html>
