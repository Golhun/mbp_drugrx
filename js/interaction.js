export function initializeInteractionSearch() {
	const drugSearch = document.getElementById("interaction-search");
	const suggestionsContainer = document.getElementById(
		"interaction-suggestions"
	);
	const selectedDrugsContainer = document.getElementById("selected-drugs");
	const checkInteractionsButton = document.getElementById("check-interactions");
	const resultsContainer = document.getElementById("interaction-results");
	const loadingIndicator = document.getElementById("loading");

	let selectedDrugs = [];
	let debounceTimeout = null;

	// Event Listeners
	drugSearch.addEventListener("input", handleDrugSearchInput);
	checkInteractionsButton.addEventListener("click", handleCheckInteractions);

	// Handle drug search input with debounce
	function handleDrugSearchInput() {
		const query = drugSearch.value.trim();
		clearTimeout(debounceTimeout);

		if (query.length < 2) {
			hideSuggestions();
			return;
		}

		debounceTimeout = setTimeout(() => fetchSuggestions(query), 500); // Increased debounce to 500ms
	}

	// Fetch suggestions from the server
	async function fetchSuggestions(query) {
		try {
			console.log("Fetching suggestions for query:", query); // Debugging log
			const response = await fetch("server.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({ type: "suggestions", query }),
			});

			const data = await response.json();
			if (data.error) throw new Error(data.error);

			renderSuggestions(data.suggestions || []);
		} catch (error) {
			console.error("Error fetching suggestions:", error.message); // Improved logging
			renderSuggestions([], error.message);
		}
	}

	// Render suggestions in the dropdown
	function renderSuggestions(suggestions, errorMessage = null) {
		if (errorMessage) {
			suggestionsContainer.innerHTML = `<div class="p-2 text-red-500">${errorMessage}</div>`;
		} else if (suggestions.length === 0) {
			suggestionsContainer.innerHTML = `<div class="p-2 text-gray-500">No results found</div>`;
		} else {
			suggestionsContainer.innerHTML = suggestions
				.map(
					(suggestion) =>
						`<div class="suggestion-item p-2 cursor-pointer hover:bg-blue-100">${suggestion}</div>`
				)
				.join("");

			suggestionsContainer
				.querySelectorAll(".suggestion-item")
				.forEach((item) => {
					item.addEventListener("click", () => addDrug(item.textContent));
				});
		}

		suggestionsContainer.classList.remove("hidden");
	}

	// Add a drug to the selected list
	function addDrug(drug) {
		if (selectedDrugs.length >= 5) {
			alert("You can only add up to 5 drugs.");
			return;
		}

		if (!selectedDrugs.includes(drug)) {
			selectedDrugs.push(drug);
			renderSelectedDrugs();
		}

		drugSearch.value = "";
		hideSuggestions();
	}

	// Render the selected drugs as bubbles
	function renderSelectedDrugs() {
		selectedDrugsContainer.innerHTML = selectedDrugs
			.map(
				(drug) =>
					`<div class="bg-blue-100 rounded-full px-3 py-1 m-1 text-sm flex items-center">
                        ${drug}
                        <button class="ml-2 text-red-500 hover:text-red-700" data-drug="${drug}">×</button>
                    </div>`
			)
			.join("");

		selectedDrugsContainer.querySelectorAll("button").forEach((button) => {
			button.addEventListener("click", () => removeDrug(button.dataset.drug));
		});
	}

	// Remove a drug from the selected list
	function removeDrug(drug) {
		selectedDrugs = selectedDrugs.filter((d) => d !== drug);
		renderSelectedDrugs();
	}

	// Hide suggestions dropdown
	function hideSuggestions() {
		suggestionsContainer.classList.add("hidden");
		suggestionsContainer.innerHTML = "";
	}

	// Handle drug interactions check request
	async function handleCheckInteractions() {
		if (selectedDrugs.length === 0) {
			alert("Please add at least one drug.");
			return;
		}

		loadingIndicator.classList.remove("hidden");
		resultsContainer.innerHTML = "";

		try {
			console.log("Checking interactions for drugs:", selectedDrugs); // Debugging log
			const response = await fetch("server.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({ type: "interactions", drugs: selectedDrugs }),
			});

			const data = await response.json();
			if (data.error) throw new Error(data.error);

			renderResults(data.db_results || []);
		} catch (error) {
			console.error("Error fetching interactions:", error.message);
			resultsContainer.innerHTML = `<p class="text-red-500">${error.message}</p>`;
		} finally {
			loadingIndicator.classList.add("hidden");
		}
	}

	// Render the results of drug interactions
	function renderResults(results) {
		if (results.length === 0) {
			resultsContainer.innerHTML = `<p class="text-gray-500">No interactions found</p>`;
		} else {
			resultsContainer.innerHTML = results
				.map(
					(result) =>
						`<div class="p-4 mb-4 bg-gray-50 border rounded-md">
                            <h3 class="font-bold">${result.drug1} ↔ ${
							result.drug2
						}</h3>
                            <p>${result.interaction_description}</p>
                            <span class="px-2 py-1 rounded ${
															result.interaction_severity === "Major"
																? "bg-red-500 text-white"
																: result.interaction_severity === "Moderate"
																? "bg-yellow-500 text-black"
																: "bg-green-500 text-white"
														}">
                                ${result.interaction_severity}
                            </span>
                        </div>`
				)
				.join("");
		}
	}
}
