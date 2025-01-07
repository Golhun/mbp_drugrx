// ./js/script.js
// This uses old-school global variables, no imports/exports.
// We define window.appState, plus "restore" functions.

window.appState = {
	interactionDrugs: [],
	interactionResults: [],
	substituteDrugs: [],
	substituteResults: [],
};

// "Restore" functions for Interactions
window.restoreInteractionState = function restoreInteractionState() {
	const selectedDrugsContainer = document.getElementById("selected-drugs");
	const resultsContainer = document.getElementById("interaction-results");
	if (selectedDrugsContainer && resultsContainer) {
		// Re-populate selected drugs
		selectedDrugsContainer.innerHTML = window.appState.interactionDrugs
			.map(
				(drug) => `
			<div class="bg-blue-100 rounded-full px-3 py-1 m-1 text-sm flex items-center">
			  ${drug}
			  <button class="ml-2 text-red-500 hover:text-red-700" data-drug="${drug}">×</button>
			</div>`
			)
			.join("");

		// Re-populate results
		resultsContainer.innerHTML = renderInteractionResults(
			window.appState.interactionResults
		);

		// Attach remove-button handlers
		selectedDrugsContainer.querySelectorAll("button").forEach((btn) => {
			btn.addEventListener("click", () =>
				removeInteractionDrug(btn.dataset.drug)
			);
		});
	}
};

function removeInteractionDrug(drug) {
	window.appState.interactionDrugs = window.appState.interactionDrugs.filter(
		(d) => d !== drug
	);
	window.restoreInteractionState();
}

function renderInteractionResults(results) {
	if (!results || !results.length) {
		return `<p class="text-gray-500">No interactions found</p>`;
	}
	return results
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

// "Restore" functions for Substitutes
window.restoreSubstituteState = function restoreSubstituteState() {
	const selectedDrugsContainer = document.getElementById(
		"selected-substitute-drugs"
	);
	const resultsContainer = document.getElementById("substitute-results");
	if (selectedDrugsContainer && resultsContainer) {
		selectedDrugsContainer.innerHTML = window.appState.substituteDrugs
			.map(
				(drug) => `
			<div class="bg-green-100 rounded-full px-3 py-1 m-1 text-sm flex items-center">
			  ${drug}
			  <button class="ml-2 text-red-500 hover:text-red-700" data-drug="${drug}">×</button>
			</div>`
			)
			.join("");

		resultsContainer.innerHTML = renderSubstituteResults(
			window.appState.substituteResults
		);

		selectedDrugsContainer.querySelectorAll("button").forEach((btn) => {
			btn.addEventListener("click", () =>
				removeSubstituteDrug(btn.dataset.drug)
			);
		});
	}
};

function removeSubstituteDrug(drug) {
	window.appState.substituteDrugs = window.appState.substituteDrugs.filter(
		(d) => d !== drug
	);
	window.restoreSubstituteState();
}

function renderSubstituteResults(results) {
	if (!results || !results.length) {
		return `<p class="text-gray-500">No substitutes found</p>`;
	}
	return results
		.map(
			(r) => `
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
	  </div>`
		)
		.join("");
}
