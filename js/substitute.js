// ./js/substitute.js
window.initializeSubstituteSearch = function initializeSubstituteSearch() {
	const substituteSearch = document.getElementById("substitute-search");
	const suggestionsContainer = document.getElementById(
		"substitute-suggestions"
	);
	const selectedDrugsContainer = document.getElementById(
		"selected-substitute-drugs"
	);
	const findSubstitutesButton = document.getElementById("find-substitutes");
	const resultsContainer = document.getElementById("substitute-results");

	let debounceTimeout = null;

	renderSelectedDrugs();
	renderResults(window.appState.substituteResults);

	substituteSearch.addEventListener("input", handleSearchInput);
	findSubstitutesButton.addEventListener("click", handleFindSubstitutes);

	function handleSearchInput() {
		const query = substituteSearch.value.trim();
		clearTimeout(debounceTimeout);
		if (query.length < 2) {
			hideSuggestions();
			return;
		}
		debounceTimeout = setTimeout(() => fetchSuggestions(query), 300);
	}

	async function fetchSuggestions(query) {
		try {
			const response = await fetch("../server.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({ type: "substitutes", query }),
			});
			const data = await response.json();
			if (data.error) throw new Error(data.error);
			renderSuggestions(data.suggestions || []);
		} catch (error) {
			console.error("Error fetching substitute suggestions:", error.message);
			renderSuggestions([], error.message);
		}
	}

	function renderSuggestions(suggestions, errorMessage = null) {
		if (errorMessage) {
			suggestionsContainer.innerHTML = `<div class="p-2 text-red-500">${errorMessage}</div>`;
		} else if (!suggestions.length) {
			suggestionsContainer.innerHTML = `<div class="p-2 text-gray-500">No results found</div>`;
		} else {
			suggestionsContainer.innerHTML = suggestions
				.map(
					(s) =>
						`<div class="suggestion-item p-2 cursor-pointer hover:bg-green-100">${s}</div>`
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

	function addDrug(drug) {
		if (window.appState.substituteDrugs.length >= 10) {
			alert("You can only add up to 10 drugs.");
			return;
		}
		if (!window.appState.substituteDrugs.includes(drug)) {
			window.appState.substituteDrugs.push(drug);
			renderSelectedDrugs();
		}
		substituteSearch.value = "";
		hideSuggestions();
	}

	function renderSelectedDrugs() {
		selectedDrugsContainer.innerHTML = window.appState.substituteDrugs
			.map(
				(drug) => `
			<div class="bg-green-100 rounded-full px-3 py-1 m-1 text-sm flex items-center">
			  ${drug}
			  <button class="ml-2 text-red-500 hover:text-red-700" data-drug="${drug}">×</button>
			</div>`
			)
			.join("");
		selectedDrugsContainer.querySelectorAll("button").forEach((btn) => {
			btn.addEventListener("click", () => removeDrug(btn.dataset.drug));
		});
	}

	function removeDrug(drug) {
		window.appState.substituteDrugs = window.appState.substituteDrugs.filter(
			(d) => d !== drug
		);
		renderSelectedDrugs();
	}

	async function handleFindSubstitutes() {
		if (!window.appState.substituteDrugs.length) {
			alert("Please add at least one drug.");
			return;
		}
		resultsContainer.innerHTML = `<p class="text-gray-500 text-center">Loading...</p>`;

		try {
			const response = await fetch("../server.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({
					type: "substitutes",
					selectedDrugs: window.appState.substituteDrugs,
				}),
			});
			const data = await response.json();
			if (data.error) throw new Error(data.error);

			window.appState.substituteResults = data.details || [];
			renderResults(window.appState.substituteResults);
		} catch (error) {
			console.error("Error fetching substitutes:", error.message);
			resultsContainer.innerHTML = `<p class="text-red-500">${error.message}</p>`;
		}
	}

	function renderResults(results) {
		if (!results || !results.length) {
			resultsContainer.innerHTML = `<p class="text-gray-500">No substitutes found</p>`;
			return;
		}

		// Build the HTML for each result in a mobile-first, card-based layout
		const cardsHTML = results
			.map((r) => {
				return `
			  <div class="p-4 bg-white border shadow-md rounded-lg my-4">
				<!-- Name with Icon -->
				<h3 class="text-xl font-bold mb-2 flex items-center space-x-2">
				  <span class="material-icons text-pink-500">medication</span>
				  <span>${r.name}</span>
				</h3>
	  
				<!-- Grid Layout for the Data -->
				<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 text-sm text-gray-700">
	  
				  <!-- Substitutes -->
				  <div class="flex items-start space-x-2">
					<span class="material-icons text-gray-500">group_work</span>
					<p>
					  <strong>Substitutes:</strong>
					  <br>
					  <span class="text-gray-600">
						${r.substitutes?.join(", ") || "N/A"}
					  </span>
					</p>
				  </div>
	  
				  <!-- Chemical Class -->
				  <div class="flex items-start space-x-2">
					<span class="material-icons text-gray-500">science</span>
					<p>
					  <strong>Chemical Class:</strong>
					  <br>
					  <span class="text-gray-600">
						${r.chemical_class || "N/A"}
					  </span>
					</p>
				  </div>
	  
				  <!-- Therapeutic Class -->
				  <div class="flex items-start space-x-2">
					<span class="material-icons text-gray-500">healing</span>
					<p>
					  <strong>Therapeutic Class:</strong>
					  <br>
					  <span class="text-gray-600">
						${r.therapeutic_class || "N/A"}
					  </span>
					</p>
				  </div>
	  
				  <!-- Action Class -->
				  <div class="flex items-start space-x-2">
					<span class="material-icons text-gray-500">auto_fix_high</span>
					<p>
					  <strong>Action Class:</strong>
					  <br>
					  <span class="text-gray-600">
						${r.action_class || "N/A"}
					  </span>
					</p>
				  </div>
	  
				  <!-- Side Effects -->
				  <div class="flex items-start space-x-2">
					<span class="material-icons text-gray-500">warning</span>
					<p>
					  <strong>Side Effects:</strong>
					  <br>
					  <span class="text-gray-600">
						${r.side_effects || "N/A"}
					  </span>
					</p>
				  </div>
	  
				  <!-- Uses -->
				  <div class="flex items-start space-x-2">
					<span class="material-icons text-gray-500">health_and_safety</span>
					<p>
					  <strong>Uses:</strong>
					  <br>
					  <span class="text-gray-600">
						${r.uses || "N/A"}
					  </span>
					</p>
				  </div>
	  
				</div> <!-- End Grid -->
			  </div>
			`;
			})
			.join("");

		// Insert the generated HTML into the results container
		resultsContainer.innerHTML = cardsHTML;
	}

	function hideSuggestions() {
		suggestionsContainer.classList.add("hidden");
		suggestionsContainer.innerHTML = "";
	}
};
