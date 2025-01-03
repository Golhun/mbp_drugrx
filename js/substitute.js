export function initializeSubstituteSearch() {
	// DOM Elements
	const drugSearch = document.getElementById("substitute-search");
	const suggestionsContainer = document.getElementById(
		"substitute-suggestions"
	);
	const resultsContainer = document.getElementById("substitute-results");
	const loadingIndicator = document.getElementById("loading");

	let debounceTimeout = null;

	// Event Listener for input in the search box
	drugSearch.addEventListener("input", () => {
		const query = drugSearch.value.trim();
		clearTimeout(debounceTimeout);

		if (query.length < 2) {
			hideSuggestions();
			return;
		}

		debounceTimeout = setTimeout(() => fetchSuggestions(query), 300);
	});

	/**
	 * Handles the input event for the drug search box.
	 */
	function handleDrugSearchInput() {
		const query = drugSearch.value.trim();
		clearTimeout(debounceTimeout);

		if (query.length < 2) {
			hideSuggestions();
			return;
		}

		debounceTimeout = setTimeout(() => fetchSubstituteSuggestions(query), 300);
	}

	/**
	 * Fetches substitute suggestions from the server.
	 * @param {string} query
	 */
	async function fetchSubstituteSuggestions(query) {
		try {
			const response = await fetch("server.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({ type: "substitutes", query }),
			});

			const data = await response.json();

			if (data.error) throw new Error(data.error);

			renderSuggestions(data.substitutes || []);
		} catch (error) {
			console.error("Error fetching substitutes:", error);
			renderSuggestions([], "Error fetching substitutes");
		}
	}

	/**
	 * Renders the list of substitute suggestions.
	 * @param {string[]} substitutes
	 * @param {string} [errorMessage]
	 */
	function renderSuggestions(substitutes, errorMessage = null) {
		if (errorMessage) {
			suggestionsContainer.innerHTML = `<div class="p-2 text-red-500">${errorMessage}</div>`;
		} else if (substitutes.length === 0) {
			suggestionsContainer.innerHTML = `<div class="p-2 text-gray-500">No substitutes found</div>`;
		} else {
			suggestionsContainer.innerHTML = substitutes
				.map(
					(substitute) => `
                <div class="suggestion-item p-2 cursor-pointer hover:bg-green-100">${substitute}</div>
            `
				)
				.join("");

			document.querySelectorAll(".suggestion-item").forEach((item, index) => {
				item.addEventListener("click", () =>
					fetchSubstituteDetails(substitutes[index])
				);
			});
		}

		suggestionsContainer.classList.remove("hidden");
	}

	/**
	 * Fetches detailed information about a selected substitute.
	 * @param {string} substitute
	 */
	async function fetchSubstituteDetails(substitute) {
		hideSuggestions();
		loadingIndicator.classList.remove("hidden");
		resultsContainer.innerHTML = "";

		try {
			const response = await fetch("server.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({ type: "substitute-details", query: substitute }),
			});

			const data = await response.json();

			if (data.error) throw new Error(data.error);

			renderResults(data.substitutes || []);
		} catch (error) {
			console.error("Error fetching substitute details:", error);
			resultsContainer.innerHTML = `<p class="text-red-500">Error fetching substitute details</p>`;
		} finally {
			loadingIndicator.classList.add("hidden");
		}
	}

	/**
	 * Renders detailed results for the selected substitute.
	 * @param {Object[]} substitutes
	 */
	function renderResults(substitutes) {
		if (substitutes.length === 0) {
			resultsContainer.innerHTML = `<p class="text-gray-500">No detailed information available for the selected substitute</p>`;
		} else {
			resultsContainer.innerHTML = substitutes
				.map(
					(substitute) => `
                <div class="border p-4 mb-4 rounded-md bg-gray-50 shadow-md">
                    <h3 class="font-bold text-lg text-green-800">Substitute: ${substitute}</h3>
                    <p class="text-gray-600">This drug can be used as an alternative.</p>
                </div>
            `
				)
				.join("");
		}
	}

	/**
	 * Hides the suggestions dropdown.
	 */
	function hideSuggestions() {
		suggestionsContainer.classList.add("hidden");
		suggestionsContainer.innerHTML = "";
	}
}
