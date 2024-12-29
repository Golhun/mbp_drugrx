// 🎯 DOM Elements
const drugSearch = document.getElementById("drug-search");
const suggestionsContainer = document.getElementById("suggestions");
const selectedDrugsContainer = document.getElementById("selected-drugs");
const checkInteractionsButton = document.getElementById("check-interactions");
const resultsContainer = document.getElementById("results");
const loadingIndicator = document.getElementById("loading");

// 🎯 State
let selectedDrugs = []; // Array to store selected drugs

// 🟢 Real-Time Drug Suggestions
drugSearch.addEventListener("input", async () => {
	const query = drugSearch.value.trim();

	// Show suggestions only if query length > 1
	if (query.length < 2) {
		suggestionsContainer.classList.add("hidden");
		suggestionsContainer.innerHTML = "";
		return;
	}

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

		// Display suggestions
		suggestionsContainer.innerHTML = "";
		data.suggestions.forEach((drug) => {
			const item = document.createElement("div");
			item.classList.add("suggestion-item");
			item.textContent = drug;
			item.addEventListener("click", () => addDrug(drug));
			suggestionsContainer.appendChild(item);
		});

		suggestionsContainer.classList.remove("hidden");
	} catch (error) {
		console.error("Suggestion Error:", error);
	}
});

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
			"m-1"
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

		displayResults(data.results);
	} catch (error) {
		loadingIndicator.classList.add("hidden");
		displayError(`Error: ${error.message}`);
	}
});

// 🟢 Display Results
function displayResults(results) {
	resultsContainer.innerHTML = "";

	if (results.length === 0) {
		resultsContainer.innerHTML = `
			<p class="text-gray-600 text-center">No interactions found for the selected drugs.</p>`;
		return;
	}

	results.forEach((result) => {
		const card = document.createElement("div");
		card.classList.add(
			"p-4",
			"bg-white",
			"shadow-md",
			"rounded-md",
			"mb-4",
			"border",
			"hover:shadow-lg"
		);

		card.innerHTML = `
            <h3 class="text-lg font-bold text-blue-600 mb-2">${result.drug1} ↔ ${result.drug2}</h3>
            <p class="text-gray-700 mb-1"><strong>Interaction:</strong> ${result.interaction_description || "N/A"}</p>
            <p class="text-gray-700 mb-1"><strong>Severity:</strong> ${result.interaction_severity || "Unknown"}</p>
        `;

		resultsContainer.appendChild(card);
	});
}

// 🟢 Display Error
function displayError(message) {
	resultsContainer.innerHTML = `
		<p class="text-red-500 text-center">${message}</p>`;
}

// 🟢 Reset Search Input
function resetSearch() {
	drugSearch.value = "";
	suggestionsContainer.innerHTML = "";
	suggestionsContainer.classList.add("hidden");
}
