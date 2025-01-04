export function initializeSubstituteSearch() {
	const substituteSearch = document.getElementById("substitute-search");
	const suggestionsContainer = document.getElementById(
		"substitute-suggestions"
	);
	const selectedDrugsContainer = document.getElementById(
		"selected-substitute-drugs"
	);
	const findSubstitutesButton = document.getElementById("find-substitutes");
	const resultsContainer = document.getElementById("substitute-results");

	let selectedDrugs = [];
	let debounceTimeout = null;

	substituteSearch.addEventListener("input", handleSearchInput);
	findSubstitutesButton.addEventListener("click", handleFindSubstitutes);

	function handleSearchInput() {
		const query = substituteSearch.value.trim();
		clearTimeout(debounceTimeout);

		if (query.length < 2) {
			hideSuggestions();
			return;
		}

		debounceTimeout = setTimeout(() => fetchSuggestions(query), 500);
	}

	async function fetchSuggestions(query) {
		try {
			const response = await fetch("server.php", {
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
		} else if (suggestions.length === 0) {
			suggestionsContainer.innerHTML = `<div class="p-2 text-gray-500">No results found</div>`;
		} else {
			suggestionsContainer.innerHTML = suggestions
				.map(
					(suggestion) =>
						`<div class="suggestion-item p-2 cursor-pointer hover:bg-green-100">${suggestion}</div>`
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
		if (selectedDrugs.length >= 5) {
			alert("You can only add up to 5 drugs.");
			return;
		}

		if (!selectedDrugs.includes(drug)) {
			selectedDrugs.push(drug);
			renderSelectedDrugs();
		}

		substituteSearch.value = "";
		hideSuggestions();
	}

	function renderSelectedDrugs() {
		selectedDrugsContainer.innerHTML = selectedDrugs
			.map(
				(drug) =>
					`<div class="bg-green-100 rounded-full px-3 py-1 m-1 text-sm flex items-center">
                        ${drug}
                        <button class="ml-2 text-red-500 hover:text-red-700" data-drug="${drug}">×</button>
                    </div>`
			)
			.join("");

		selectedDrugsContainer.querySelectorAll("button").forEach((button) => {
			button.addEventListener("click", () => removeDrug(button.dataset.drug));
		});
	}

	function removeDrug(drug) {
		selectedDrugs = selectedDrugs.filter((d) => d !== drug);
		renderSelectedDrugs();
	}

    async function handleFindSubstitutes() {
        if (selectedDrugs.length === 0) {
            alert("Please add at least one drug.");
            return;
        }
    
        resultsContainer.innerHTML = `<p class="text-gray-500 text-center">Loading...</p>`;
    
        try {
            const response = await fetch("server.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ type: "substitutes", selectedDrugs }),
            });
    
            const data = await response.json();
            if (data.error) throw new Error(data.error);
    
            renderResults(data.details || []);
        } catch (error) {
            console.error("Error fetching substitutes:", error.message);
            resultsContainer.innerHTML = `<p class="text-red-500">${error.message}</p>`;
        }
    }
    

	function renderResults(results) {
		if (results.length === 0) {
			resultsContainer.innerHTML = `<p class="text-gray-500">No substitutes found</p>`;
		} else {
			resultsContainer.innerHTML = results
				.map(
					(result) =>
						`<div class="p-4 bg-white border shadow-md rounded-lg">
                            <h3 class="text-lg font-bold mb-2">${
															result.name
														}</h3>
                            <p><strong>Substitutes:</strong> ${result.substitutes.join(
															", "
														)}</p>
                            <p><strong>Chemical Class:</strong> ${
															result.chemical_class
														}</p>
                            <p><strong>Therapeutic Class:</strong> ${
															result.therapeutic_class
														}</p>
                            <p><strong>Action Class:</strong> ${
															result.action_class
														}</p>
                            <p><strong>Side Effects:</strong> ${
															result.side_effects
														}</p>
                            <p><strong>Uses:</strong> ${result.uses}</p>
                        </div>`
				)
				.join("");
		}
	}

	function hideSuggestions() {
		suggestionsContainer.classList.add("hidden");
		suggestionsContainer.innerHTML = "";
	}
}
