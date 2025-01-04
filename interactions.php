<section>
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Search Drug Interactions</h2>

    <!-- Input for Searching Drugs -->
    <div class="relative mb-4">
        <input type="text" id="interaction-search"
               class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Type drug names to check interactions...">
        <div id="interaction-suggestions" 
             class="absolute bg-white border rounded-md mt-1 w-full shadow-lg hidden overflow-y-auto suggestions z-50">
        </div>
    </div>

    <!-- Selected Drugs -->
    <div id="selected-drugs" class="flex flex-wrap gap-2 mb-4"></div>

    <!-- Check Interactions Button -->
    <button id="check-interactions"
            class="w-full bg-gradient-to-r from-blue-900 to-blue-800 text-white py-3 rounded-md hover:from-blue-700 hover:to-blue-600">
        Check Interactions
    </button>

    <!-- Loading Indicator -->
    <section id="loading" class="hidden mt-4">
        <div class="flex justify-center items-center space-x-2">
            <div class="h-6 w-6 rounded-full bg-blue-400 animate-ping"></div>
            <div class="h-6 w-6 rounded-full bg-blue-600 animate-ping"></div>
            <div class="h-6 w-6 rounded-full bg-blue-800 animate-ping"></div>
        </div>
        <p class="text-center text-gray-600 mt-2">Fetching interactions... Please wait.</p>
    </section>

    <!-- Interaction Results -->
    <section id="interaction-results" class="mt-6 space-y-6"></section>
</section>
