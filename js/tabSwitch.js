export function initializeTabSwitching() {
	const tabs = document.querySelectorAll(".tabs button");
	const tabContentContainer = document.getElementById("tab-content-container");

	// Set initial active tab content
	loadTabContent("interactions");

	tabs.forEach((tab) => {
		tab.addEventListener("click", () => {
			tabs.forEach((t) =>
				t.classList.remove("border-blue-500", "text-blue-600", "active")
			);
			tab.classList.add("border-blue-500", "text-blue-600", "active");

			const selectedTab =
				tab.getAttribute("id") === "tab-interactions"
					? "interactions"
					: "substitutes";
			loadTabContent(selectedTab);
		});
	});

	/**
	 * Dynamically loads content based on the selected tab.
	 * @param {string} tab - The selected tab ("interactions" or "substitutes").
	 */
	async function loadTabContent(tab) {
		tabContentContainer.classList.add("opacity-50", "scale-95"); // Add transition classes
		let content;

		if (tab === "interactions") {
			content = await fetch("interactions.php").then((res) => res.text());
		} else {
			content = await fetch("substitutes.php").then((res) => res.text());
		}

		tabContentContainer.innerHTML = content;
		setTimeout(() => {
			tabContentContainer.classList.remove("opacity-50", "scale-95"); // Restore visibility
		}, 100); // Shortened transition delay for smoother experience
	}
}
