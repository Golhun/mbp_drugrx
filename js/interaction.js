// ./js/interaction.js

window.initializeInteractionSearch = function initializeInteractionSearch() {
	const drugSearch = document.getElementById("interaction-search");
	const suggestionsContainer = document.getElementById(
		"interaction-suggestions"
	);
	const selectedDrugsContainer = document.getElementById("selected-drugs");
	const checkInteractionsBtn = document.getElementById("check-interactions");
	const resultsContainer = document.getElementById("interaction-results");
	const loadingIndicator = document.getElementById("loading");

	let debounceTimeout = null;

	renderSelectedDrugs();
	renderResults(window.appState.interactionResults);

	drugSearch.addEventListener("input", handleDrugSearchInput);
	checkInteractionsBtn.addEventListener("click", handleCheckInteractions);

	function handleDrugSearchInput() {
		const query = drugSearch.value.trim();
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
				body: JSON.stringify({ type: "suggestions", query }),
			});

			const data = await response.json();
			if (data.error) throw new Error(data.error);
			renderSuggestions(data.suggestions || []);
		} catch (error) {
			console.error("Error fetching suggestions:", error.message);
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
						`<div class="suggestion-item p-2 cursor-pointer hover:bg-blue-100">${s}</div>`
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
		if (window.appState.interactionDrugs.length >= 10) {
			alert("You can only add up to 10 drugs.");
			return;
		}
		if (!window.appState.interactionDrugs.includes(drug)) {
			window.appState.interactionDrugs.push(drug);
			renderSelectedDrugs();
		}
		drugSearch.value = "";
		hideSuggestions();
	}

	function renderSelectedDrugs() {
		selectedDrugsContainer.innerHTML = window.appState.interactionDrugs
			.map((drug) => {
				return `
			<div
			  class="bg-blue-100 rounded-full px-3 py-1 m-1 text-sm flex items-center"
			  data-drug-bubble
			  data-drug-name="${drug}"
			>
			  ${drug}
			  <button class="ml-2 text-red-500 hover:text-red-700" data-drug="${drug}">×</button>
			</div>
		  `;
			})
			.join("");

		selectedDrugsContainer.querySelectorAll("button").forEach((btn) => {
			btn.addEventListener("click", () => removeDrug(btn.dataset.drug));
		});

		// After rendering new bubbles, attach bubble info
		if (window.attachBubbleInfo) {
			window.attachBubbleInfo("#selected-drugs");
		}
	}

	function removeDrug(drug) {
		window.appState.interactionDrugs = window.appState.interactionDrugs.filter(
			(d) => d !== drug
		);
		renderSelectedDrugs();
	}

	function hideSuggestions() {
		suggestionsContainer.classList.add("hidden");
		suggestionsContainer.innerHTML = "";
	}

	async function handleCheckInteractions() {
		if (window.appState.interactionDrugs.length < 2) {
			alert("Please add at least two drugs to check interactions.");
			return;
		}
		loadingIndicator.classList.remove("hidden");
		resultsContainer.innerHTML = "";

		try {
			const response = await fetch("../server.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({
					type: "interactions",
					drugs: window.appState.interactionDrugs,
				}),
			});
			const data = await response.json();
			if (data.error) throw new Error(data.error);

			window.appState.interactionResults = data.db_results || [];
			renderResults(window.appState.interactionResults);
		} catch (error) {
			console.error("Error fetching interactions:", error.message);
			resultsContainer.innerHTML = `<p class="text-red-500">${error.message}</p>`;
		} finally {
			loadingIndicator.classList.add("hidden");
		}
	}

	function renderResults(results) {
		if (!results || !results.length) {
			resultsContainer.innerHTML = `<p class="text-gray-500">No interactions found</p>`;
		} else {
			resultsContainer.innerHTML = results
				.map(
					(r) => `
			  <div class="p-4 mb-4 bg-gray-50 border rounded-md">
				<h3 class="font-bold">${r.drug1} ↔ ${r.drug2}</h3>
				<p>${r.interaction_description}</p>
				<span class="px-2 py-1 rounded ${
					r.interaction_severity === "Major"
						? "bg-red-500 text-white"
						: r.interaction_severity === "Moderate"
						? "bg-yellow-500 text-black"
						: "bg-green-500 text-white"
				}">${r.interaction_severity}</span>
			  </div>`
				)
				.join("");
		}
	}
};
