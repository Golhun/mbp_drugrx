<section>
    <h2 class="text-2xl font-bold mb-4 text-gray-700">Search Drug Substitutes</h2>

    <div class="relative mb-4">
        <input type="text" id="substitute-search"
               class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
               placeholder="Type a drug name to find substitutes...">
        <div id="substitute-suggestions"
             class="absolute bg-white border rounded-md mt-1 w-full shadow-lg hidden overflow-y-auto suggestions z-50">
        </div>
    </div>

    <!-- Substitutes Results Section -->
    <section id="substitute-results" class="mt-6 space-y-6"></section>
</section>
