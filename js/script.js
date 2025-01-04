// Import functions
import { initializeInteractionSearch } from "./interaction.js";
import { initializeSubstituteSearch } from "./substitute.js";

// Global State
export const appState = {
	interactionDrugs: [],
	interactionResults: [], // Should contain an array of interaction result objects
	substituteDrugs: [],
	substituteResults: [], // Should contain an array of substitute result objects
};

// Preserve state on tab switching
document.addEventListener("DOMContentLoaded", () => {
	console.log("Initializing application...");

	// Set up tab switching
	initializeTabSwitching();

	// Load and initialize the default tab (interactions)
	loadTabContent("interactions");
});

function initializeTabSwitching() {
	const tabs = document.querySelectorAll(".tabs button");
	const tabContentContainer = document.getElementById("tab-content-container");

	tabs.forEach((tab) => {
		tab.addEventListener("click", () => {
			// Add smooth transition
			tabContentContainer.classList.add("opacity-50", "scale-95");

			// Switch active tab
			tabs.forEach((t) =>
				t.classList.remove("active", "border-blue-500", "text-blue-600")
			);
			tab.classList.add("active", "border-blue-500", "text-blue-600");

			const selectedTab =
				tab.id === "tab-interactions" ? "interactions" : "substitutes";

			// Load the tab content
			setTimeout(() => loadTabContent(selectedTab), 200); // Delay for smooth animation
		});
	});
}

async function loadTabContent(tab) {
	const tabContentContainer = document.getElementById("tab-content-container");

	// Add loading state
	tabContentContainer.innerHTML = `<p class="text-gray-500 text-center">Loading...</p>`;

	try {
		const response = await fetch(`${tab}.php`);
		const content = await response.text();
		tabContentContainer.innerHTML = content;

		// Dynamically initialize scripts
		if (tab === "interactions") {
			initializeInteractionSearch();
			restoreInteractionState();
		} else if (tab === "substitutes") {
			initializeSubstituteSearch();
			restoreSubstituteState();
		}
	} catch (error) {
		console.error(`Failed to load content for ${tab}:`, error);
		tabContentContainer.innerHTML = `<p class="text-red-500 text-center">Failed to load content. Please try again.</p>`;
	} finally {
		// Restore visibility
		tabContentContainer.classList.remove("opacity-50", "scale-95");
	}
}

function restoreInteractionState() {
	const selectedDrugsContainer = document.getElementById("selected-drugs");
	const resultsContainer = document.getElementById("interaction-results");

	// Restore selected drugs
	selectedDrugsContainer.innerHTML = appState.interactionDrugs
		.map(
			(drug) =>
				`<div class="bg-blue-100 rounded-full px-3 py-1 m-1 text-sm flex items-center">
                    ${drug}
                    <button class="ml-2 text-red-500 hover:text-red-700" data-drug="${drug}">×</button>
                </div>`
		)
		.join("");

	// Restore results
	resultsContainer.innerHTML = renderInteractionResults(
		appState.interactionResults
	);

	// Add event listeners to remove drug buttons
	selectedDrugsContainer.querySelectorAll("button").forEach((button) => {
		button.addEventListener("click", () =>
			removeInteractionDrug(button.dataset.drug)
		);
	});
}

function restoreSubstituteState() {
	const selectedDrugsContainer = document.getElementById(
		"selected-substitute-drugs"
	);
	const resultsContainer = document.getElementById("substitute-results");

	// Restore selected drugs
	selectedDrugsContainer.innerHTML = appState.substituteDrugs
		.map(
			(drug) =>
				`<div class="bg-green-100 rounded-full px-3 py-1 m-1 text-sm flex items-center">
                    ${drug}
                    <button class="ml-2 text-red-500 hover:text-red-700" data-drug="${drug}">×</button>
                </div>`
		)
		.join("");

	// Restore results
	resultsContainer.innerHTML = renderSubstituteResults(
		appState.substituteResults
	);

	// Add event listeners to remove drug buttons
	selectedDrugsContainer.querySelectorAll("button").forEach((button) => {
		button.addEventListener("click", () =>
			removeSubstituteDrug(button.dataset.drug)
		);
	});
}

function removeInteractionDrug(drug) {
	appState.interactionDrugs = appState.interactionDrugs.filter(
		(d) => d !== drug
	);
	restoreInteractionState();
}

function removeSubstituteDrug(drug) {
	appState.substituteDrugs = appState.substituteDrugs.filter((d) => d !== drug);
	restoreSubstituteState();
}

function renderInteractionResults(results) {
	if (results.length === 0) {
		return `<p class="text-gray-500">No interactions found</p>`;
	}

	return results
		.map(
			(result) =>
				`<div class="p-4 bg-gray-50 border shadow-md rounded-lg">
                    <h3 class="font-bold mb-2">${result.drug1} ↔ ${
					result.drug2
				}</h3>
                    <p>${result.interaction_description}</p>
                    <span class="px-2 py-1 rounded ${
											result.interaction_severity === "Major"
												? "bg-red-500 text-white"
												: result.interaction_severity === "Moderate"
												? "bg-yellow-500 text-black"
												: "bg-green-500 text-white"
										}">${result.interaction_severity}</span>
                </div>`
		)
		.join("");
}

function renderSubstituteResults(results) {
	if (results.length === 0) {
		return `<p class="text-gray-500">No substitutes found</p>`;
	}

	return results
		.map(
			(result) =>
				`<div class="p-4 bg-white border shadow-md rounded-lg">
                    <h3 class="text-lg font-bold mb-2">${result.name}</h3>
                    <p><strong>Substitutes:</strong> ${
											result.substitutes?.join(", ") || "N/A"
										}</p>
                    <p><strong>Chemical Class:</strong> ${
											result.chemical_class || "N/A"
										}</p>
                    <p><strong>Therapeutic Class:</strong> ${
											result.therapeutic_class || "N/A"
										}</p>
                    <p><strong>Action Class:</strong> ${
											result.action_class || "N/A"
										}</p>
                    <p><strong>Side Effects:</strong> ${
											result.side_effects || "N/A"
										}</p>
                    <p><strong>Uses:</strong> ${result.uses || "N/A"}</p>
                </div>`
		)
		.join("");
}
