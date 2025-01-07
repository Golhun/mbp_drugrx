// ./js/substitute.js
window.initializeSubstituteSearch = function initializeSubstituteSearch() {
	const substituteSearch       = document.getElementById("substitute-search");
	const suggestionsContainer   = document.getElementById("substitute-suggestions");
	const selectedDrugsContainer = document.getElementById("selected-substitute-drugs");
	const findSubstitutesButton  = document.getElementById("find-substitutes");
	const resultsContainer       = document.getElementById("substitute-results");
  
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
		  body: JSON.stringify({ type: "substitutes", query })
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
			(s) => `<div class="suggestion-item p-2 cursor-pointer hover:bg-green-100">${s}</div>`
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
			selectedDrugs: window.appState.substituteDrugs
		  })
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
	  } else {
		resultsContainer.innerHTML = results
		  .map(
			(r) => `
			  <div class="p-4 bg-white border shadow-md rounded-lg">
				<h3 class="text-lg font-bold mb-2">${r.name}</h3>
				<p><strong>Substitutes:</strong> ${r.substitutes?.join(", ") || "N/A"}</p>
				<p><strong>Chemical Class:</strong> ${r.chemical_class || "N/A"}</p>
				<p><strong>Therapeutic Class:</strong> ${r.therapeutic_class || "N/A"}</p>
				<p><strong>Action Class:</strong> ${r.action_class || "N/A"}</p>
				<p><strong>Side Effects:</strong> ${r.side_effects || "N/A"}</p>
				<p><strong>Uses:</strong> ${r.uses || "N/A"}</p>
			  </div>`
		  )
		  .join("");
	  }
	}
  
	function hideSuggestions() {
	  suggestionsContainer.classList.add("hidden");
	  suggestionsContainer.innerHTML = "";
	}
  };
  