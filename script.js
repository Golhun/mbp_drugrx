// 🎯 DOM Elements
const drugSearch = document.getElementById("drug-search");
const suggestionsContainer = document.getElementById("suggestions");
const selectedDrugsContainer = document.getElementById("selected-drugs");
const checkInteractionsButton = document.getElementById("check-interactions");
const resultsContainer = document.getElementById("results");
const loadingIndicator = document.getElementById("loading");

// 🎯 State Management
let selectedDrugs = []; // Stores selected drugs
let debounceTimeout = null; // For debouncing search queries

// 🟢 Real-Time Drug Suggestions with Debouncing
drugSearch.addEventListener("input", () => {
	const query = drugSearch.value.trim();

	// Clear previous timeout to prevent unnecessary API calls
	clearTimeout(debounceTimeout);

	if (query.length < 2) {
		suggestionsContainer.classList.add("hidden");
		suggestionsContainer.innerHTML = "";
		return;
	}

	// Debounce API call
	debounceTimeout = setTimeout(async () => {
		await fetchSuggestions(query);
	}, 300); // 300ms debounce delay
});

// 🟢 Fetch Suggestions from Backend
async function fetchSuggestions(query) {
	try {
		const response = await fetch("server.php", {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({ type: "suggestions", query }),
		});

		if (!response.ok) {
			throw new Error("Failed to fetch suggestions.");
		}

		const data = await response.json();

		if (data.error) {
			throw new Error(data.error);
		}

		// Display Suggestions
		suggestionsContainer.innerHTML = "";
		if (data.suggestions.length === 0) {
			suggestionsContainer.innerHTML = `<div class="suggestion-item text-gray-500 p-2">No results found</div>`;
		} else {
			data.suggestions.forEach((drug) => {
				const item = document.createElement("div");
				item.classList.add(
					"suggestion-item",
					"cursor-pointer",
					"hover:bg-blue-100",
					"p-2"
				);
				item.textContent = drug;
				item.addEventListener("click", () => addDrug(drug));
				suggestionsContainer.appendChild(item);
			});
		}

		suggestionsContainer.classList.remove("hidden");
	} catch (error) {
		console.error("Suggestion Error:", error);
		suggestionsContainer.innerHTML = `<div class="suggestion-item text-red-500 p-2">Error fetching suggestions</div>`;
	}
}

// 🟢 Add Drug to Selected List
function addDrug(drug) {
	if (!selectedDrugs.includes(drug)) {
		selectedDrugs.push(drug);
		renderSelectedDrugs();
	}

	drugSearch.value = "";
	suggestionsContainer.classList.add("hidden");
}

// 🟢 Remove Drug from Selected List
function removeDrug(drug) {
	selectedDrugs = selectedDrugs.filter((d) => d !== drug);
	renderSelectedDrugs();
}

// 🟢 Render Selected Drugs as Bubbles
function renderSelectedDrugs() {
	selectedDrugsContainer.innerHTML = "";

	selectedDrugs.forEach((drug) => {
		const bubble = document.createElement("div");
		bubble.classList.add(
			"bubble",
			"inline-flex",
			"items-center",
			"bg-blue-100",
			"rounded-full",
			"px-3",
			"py-1",
			"m-1",
			"text-sm"
		);
		bubble.innerHTML = `
            <span>${drug}</span>
            <button onclick="removeDrug('${drug}')" class="ml-2 text-red-500 hover:text-red-700">×</button>
        `;
		selectedDrugsContainer.appendChild(bubble);
	});
}

// 🟢 Check Drug Interactions
checkInteractionsButton.addEventListener("click", async () => {
	if (selectedDrugs.length === 0) {
		alert("⚠️ Please add at least one drug to check interactions.");
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

		if (!response.ok) {
			throw new Error("Failed to fetch interactions.");
		}

		const data = await response.json();
		loadingIndicator.classList.add("hidden");

		if (data.error) {
			throw new Error(data.error);
		}

		displayResults(data.db_results || [], data.api_results || []);
	} catch (error) {
		loadingIndicator.classList.add("hidden");
		displayError(`Error: ${error.message}`);
	}
});

// 🟢 Display Interaction Results
function displayResults(dbResults, apiResults) {
	resultsContainer.innerHTML = "";

	// 🟢 Database Results
	if (dbResults.length > 0) {
		resultsContainer.innerHTML += `<h2 class="text-lg font-bold text-gray-700 mt-4">📊 Database Results</h2>`;
		dbResults.forEach((result) => {
			const card = document.createElement("div");
			card.classList.add(
				"p-4",
				"bg-white",
				"shadow-md",
				"rounded-md",
				"mb-4",
				"border-l-4",
				"border-green-500"
			);
			card.innerHTML = `
                <h3 class="text-lg font-bold text-blue-600 mb-2">${
									result.drug1
								} ↔ ${result.drug2}</h3>
                <p class="text-gray-700"><strong>Interaction:</strong> ${
									result.interaction_description || "N/A"
								}</p>
                <p class="text-gray-700"><strong>Severity:</strong> ${
									result.interaction_severity || "Unknown"
								}</p>
            `;
			resultsContainer.appendChild(card);
		});
	}

	// 🟢 API Results
	if (apiResults.length > 0) {
		resultsContainer.innerHTML += `<h2 class="text-lg font-bold text-gray-700 mt-4">🌐 API Results</h2>`;
		apiResults.forEach((result) => {
			const card = document.createElement("div");
			card.classList.add(
				"p-4",
				"bg-white",
				"shadow-md",
				"rounded-md",
				"mb-4",
				"border-l-4",
				"border-blue-500"
			);
			card.innerHTML = `
                <h3 class="text-lg font-bold text-blue-600 mb-2">${
									result.drug
								}</h3>
                <p><strong>Warnings:</strong> ${result.warnings || "N/A"}</p>
            `;
			resultsContainer.appendChild(card);
		});
	}
}

// 🟢 Display Error Message
function displayError(message) {
	resultsContainer.innerHTML = `<p class="text-red-500 text-center">${message}</p>`;
}
