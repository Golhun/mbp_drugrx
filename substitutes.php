<section>
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Search Drug Substitutes</h2>

    <!-- Input for Searching Drugs -->
    <div class="relative mb-4">
        <input type="text" id="substitute-search"
               class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
               placeholder="Type brand names to find substitutes...">
        <div id="substitute-suggestions" 
             class="absolute bg-white border rounded-md mt-1 w-full shadow-lg hidden overflow-y-auto suggestions z-50">
        </div>
    </div>

    <!-- Selected Drugs -->
    <div id="selected-substitute-drugs" class="flex flex-wrap gap-2 mb-4"></div>

    <!-- Find Substitutes Button -->
    <button id="find-substitutes"
            class="w-full bg-gradient-to-r from-green-900 to-green-800 text-white py-3 rounded-md hover:from-green-700 hover:to-green-600">
        Find Substitutes
    </button>

    <!-- Substitutes Results Section -->
    <section id="substitute-results" class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"></section>
</section>
