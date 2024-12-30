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

		if (!response.ok) throw new Error("Failed to fetch suggestions.");

		const data = await response.json();

		if (data.error) throw new Error(data.error);

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

		if (!response.ok) throw new Error("Failed to fetch interactions.");

		const data = await response.json();
		loadingIndicator.classList.add("hidden");

		if (data.error) throw new Error(data.error);

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
	dbResults.forEach((result) => {
		resultsContainer.innerHTML += generateInteractionCard(
			result.drug1,
			result.drug2,
			result.interaction_description,
			result.interaction_severity,
			apiResults
		);
	});
}

// 🟢 Generate Interaction Card
function generateInteractionCard(
	drug1,
	drug2,
	description,
	severity,
	apiResults
) {
	const severityClass =
		{
			major: "bg-red-600",
			moderate: "bg-yellow-500",
			minor: "bg-green-500",
		}[severity.toLowerCase()] || "bg-gray-500";

	const apiDescriptions = apiResults
		.map((api) => `<td class="p-2 border">${api.description}</td>`)
		.join("");
	const apiWarnings = apiResults
		.map((api) => `<td class="p-2 border">${api.warnings}</td>`)
		.join("");

	return `
    <div class="max-w-[1160px] mx-auto space-y-6 bg-white rounded-lg shadow-md border border-gray-200 p-4 md:p-6">
        <h1 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">Interaction Info</h1>
        <div class="flex items-center text-lg font-semibold gap-2">
            <span>SEVERITY</span> | 
            <span class="${severityClass} text-white text-sm px-3 py-1 rounded-full">${severity.toUpperCase()}</span>
        </div>
        <p class="text-gray-600">${description}</p>
        <table class="w-full border text-sm mt-4">
            <tr><th>${drug1}</th><th>${drug2}</th></tr>
            <tr>${apiDescriptions}</tr>
            <tr>${apiWarnings}</tr>
        </table>
    </div>`;
}

// 🟢 Display Error Message
function displayError(message) {
	resultsContainer.innerHTML = `<p class="text-red-500 text-center">${message}</p>`;
}
