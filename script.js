// 🎯 DOM Elements
const submitButton = document.getElementById("submit-button");
const drugsInput = document.getElementById("drugs");
const resultsContainer = document.getElementById("results");
const loadingIndicator = document.getElementById("loading");

// 🎯 Event Listener for Interaction Check
submitButton.addEventListener("click", async function () {
	const drugs = drugsInput.value.trim();

	if (!drugs) {
		alert("⚠️ Please enter at least one drug name.");
		return;
	}

	resultsContainer.innerHTML = "";
	loadingIndicator.classList.remove("hidden");

	try {
		const results = await fetchDrugInteractions(drugs);
		displayResults(results);
	} catch (error) {
		displayError(`Error: ${error.message}`);
	} finally {
		loadingIndicator.classList.add("hidden");
	}
});

// 🟢 Fetch Data from OpenFDA API
async function fetchDrugInteractions(drugs) {
	const response = await fetch("server.php", {
		method: "POST",
		headers: { "Content-Type": "application/json" },
		body: JSON.stringify({ drugs }),
	});

	if (!response.ok) {
		throw new Error("Failed to fetch data from the server.");
	}

	const data = await response.json();

	if (data.error) {
		throw new Error(data.error);
	}

	return data.results || [];
}

// 🟢 Display Results
function displayResults(results) {
	resultsContainer.innerHTML = "";

	if (results.length === 0) {
		resultsContainer.innerHTML = `<p class="text-gray-600 text-center">No results found for the entered drugs.</p>`;
		return;
	}

	results.forEach((result) => {
		const card = document.createElement("div");
		card.classList.add("p-4", "bg-white", "shadow-md", "rounded-md", "mb-4");

		card.innerHTML = `
            <h3 class="text-lg font-bold text-blue-600">${result.drug}</h3>
            <p class="text-gray-700"><strong>Warnings:</strong> ${
							result.warnings || "N/A"
						}</p>
            <p class="text-gray-700"><strong>Interactions:</strong> ${
							result.interactions || "N/A"
						}</p>
            ${result.error ? `<p class="text-red-500">${result.error}</p>` : ""}
        `;

		resultsContainer.appendChild(card);
	});
}

// 🟢 Display Error
function displayError(message) {
	resultsContainer.innerHTML = `<p class="text-red-500 text-center">${message}</p>`;
}
