import { initializeInteractionSearch } from "./interaction.js";
import { initializeSubstituteSearch } from "./substitute.js";

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
			// Remove active class from all tabs and add it to the clicked tab
			tabs.forEach((t) =>
				t.classList.remove("active", "border-blue-500", "text-blue-600")
			);
			tab.classList.add("active", "border-blue-500", "text-blue-600");

			const selectedTab =
				tab.id === "tab-interactions" ? "interactions" : "substitutes";
			loadTabContent(selectedTab);
		});
	});
}

async function loadTabContent(tab) {
	const tabContentContainer = document.getElementById("tab-content-container");

	// Add loading indicator
	tabContentContainer.innerHTML = `<p class="text-gray-500 text-center">Loading...</p>`;

	try {
		// Dynamically fetch tab content
		const response = await fetch(`${tab}.php`);
		const content = await response.text();
		tabContentContainer.innerHTML = content;

		// Dynamically initialize tab-specific scripts
		if (tab === "interactions") {
			initializeInteractionSearch();
			console.log("Interaction search initialized.");
		} else if (tab === "substitutes") {
			initializeSubstituteSearch();
			console.log("Substitute search initialized.");
		}
	} catch (error) {
		console.error(`Failed to load content for ${tab}:`, error);
		tabContentContainer.innerHTML = `<p class="text-red-500 text-center">Failed to load content. Please try again.</p>`;
	}
}
