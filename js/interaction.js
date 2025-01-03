export function initializeInteractionSearch() {
	// DOM Elements
	const drugSearch = document.getElementById("interaction-search");
	const suggestionsContainer = document.getElementById(
		"interaction-suggestions"
	);
	const selectedDrugsContainer = document.getElementById("selected-drugs");
	const checkInteractionsButton = document.getElementById("check-interactions");
	const resultsContainer = document.getElementById("interaction-results");
	const loadingIndicator = document.getElementById("loading");

	// State
	let selectedDrugs = [];
	let debounceTimeout = null;

	// Event Listeners
	drugSearch.addEventListener("input", handleDrugSearchInput);
	checkInteractionsButton.addEventListener("click", handleCheckInteractions);

	/**
	 * Handles input event for drug search.
	 */
	function handleDrugSearchInput() {
		const query = drugSearch.value.trim();
		clearTimeout(debounceTimeout);

		if (query.length < 2) {
			hideSuggestions();
			return;
		}

		debounceTimeout = setTimeout(() => fetchSuggestions(query), 300);
	}

	/**
	 * Fetches suggestions from the server.
	 * @param {string} query
	 */
	async function fetchSuggestions(query) {
		try {
			const response = await fetch("server.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({ type: "suggestions", query }),
			});

			const data = await response.json();

			if (data.error) throw new Error(data.error);
			renderSuggestions(data.suggestions || []);
		} catch (error) {
			console.error("Error fetching suggestions:", error);
			renderSuggestions([], "Error fetching suggestions");
		}
	}

	/**
	 * Renders suggestions in the dropdown.
	 * @param {string[]} suggestions
	 * @param {string} [errorMessage]
	 */
	function renderSuggestions(suggestions, errorMessage = null) {
		if (errorMessage) {
			suggestionsContainer.innerHTML = `<div class="p-2 text-red-500">${errorMessage}</div>`;
		} else if (suggestions.length === 0) {
			suggestionsContainer.innerHTML = `<div class="p-2 text-gray-500">No results found</div>`;
		} else {
			suggestionsContainer.innerHTML = suggestions
				.map(
					(suggestion) => `
                <div class="suggestion-item p-2 cursor-pointer hover:bg-blue-100">${suggestion}</div>
            `
				)
				.join("");

			// Add click event to each suggestion
			document.querySelectorAll(".suggestion-item").forEach((item, index) => {
				item.addEventListener("click", () => addDrug(suggestions[index]));
			});
		}

		suggestionsContainer.classList.remove("hidden");
	}

	/**
	 * Adds a drug to the selected list.
	 * @param {string} drug
	 */
	function addDrug(drug) {
		if (!selectedDrugs.includes(drug)) {
			selectedDrugs.push(drug);
			renderSelectedDrugs();
		}

		drugSearch.value = "";
		hideSuggestions();
	}

	/**
	 * Renders selected drugs as chips.
	 */
	function renderSelectedDrugs() {
		selectedDrugsContainer.innerHTML = selectedDrugs
			.map(
				(drug) => `
            <div class="inline-flex items-center bg-blue-100 rounded-full px-3 py-1 m-1 text-sm">
                <span>${drug}</span>
                <button class="ml-2 text-red-500 hover:text-red-700" onclick="removeDrug('${drug}')">×</button>
            </div>
        `
			)
			.join("");
	}

	/**
	 * Removes a drug from the selected list.
	 * @param {string} drug
	 */
	window.removeDrug = function (drug) {
		selectedDrugs = selectedDrugs.filter((d) => d !== drug);
		renderSelectedDrugs();
	};

	/**
	 * Hides the suggestions container.
	 */
	function hideSuggestions() {
		suggestionsContainer.classList.add("hidden");
		suggestionsContainer.innerHTML = "";
	}

	/**
	 * Handles "Check Interactions" button click.
	 */
	async function handleCheckInteractions() {
		if (selectedDrugs.length === 0) {
			alert("Please add at least one drug.");
			return;
		}

		resultsContainer.innerHTML = "";
		loadingIndicator.classList.remove("hidden");

		try {
			const response = await fetch("server.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({ type: "interactions", drugs: selectedDrugs }),
			});

			const data = await response.json();
			if (data.error) throw new Error(data.error);

			renderResults(data.db_results || []);
		} catch (error) {
			console.error("Error fetching interactions:", error);
			resultsContainer.innerHTML = `<p class="text-red-500">Error fetching interactions</p>`;
		} finally {
			loadingIndicator.classList.add("hidden");
		}
	}

	/**
	 * Renders interaction results.
	 * @param {Object[]} results
	 */
	function renderResults(results) {
		if (results.length === 0) {
			resultsContainer.innerHTML = `<p class="text-gray-500">No interactions found</p>`;
		} else {
			resultsContainer.innerHTML = results
				.map(
					(result) => `
                <div class="border p-4 mb-4 rounded-md bg-gray-50 shadow-md">
                    <h3 class="font-bold text-lg text-blue-800">
                        Interaction: ${result.drug1} ↔ ${result.drug2}
                    </h3>
                    <p class="text-gray-600">${
											result.interaction_description
										}</p>
                    <span class="inline-block mt-2 px-3 py-1 rounded-full ${
											result.interaction_severity === "Major"
												? "bg-red-500 text-white"
												: result.interaction_severity === "Moderate"
												? "bg-yellow-500 text-white"
												: "bg-green-500 text-white"
										}">
                        ${result.interaction_severity}
                    </span>
                </div>
            `
				)
				.join("");
		}
	}
}
