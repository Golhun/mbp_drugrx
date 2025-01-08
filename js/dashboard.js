// ./js/dashboard.js
// No imports. We'll rely on global window.initializeInteractionSearch, etc.

document.addEventListener("DOMContentLoaded", () => {
	// Default to loading "index.php" (Drug Info partial)
	loadContent("index.php");

	const navDrugInfo = document.getElementById("nav-drug-info");
	const navProfile = document.getElementById("nav-profile");
	const navBlog = document.getElementById("nav-blog");

	navDrugInfo.addEventListener("click", () => loadContent("index.php"));
	navProfile.addEventListener("click", () => loadContent("profile.php"));
	navBlog.addEventListener("click", () => loadContent("blog_page.php"));
});

/**
 * Load a partial (index.php or profile.php) into #content-area, then init sub-tabs if needed
 */
function loadContent(url) {
	const contentArea = document.getElementById("content-area");
	if (!contentArea) return;

	contentArea.classList.add("opacity-50", "scale-95");
	contentArea.innerHTML = `<p class="text-gray-500 text-center">Loading...</p>`;

	setTimeout(() => {
		fetch(url)
			.then((resp) => {
				if (!resp.ok) {
					throw new Error(`Failed to load ${url}`);
				}
				return resp.text();
			})
			.then((html) => {
				contentArea.innerHTML = html;

				// If we loaded "index.php", set up the sub-tab system
				if (url === "index.php") {
					initDrugInfoTabs();
				}

				contentArea.classList.remove("opacity-50", "scale-95");
			})
			.catch((err) => {
				console.error("Error loading partial:", err);
				contentArea.innerHTML = `<p class="text-red-500 text-center">Error loading ${url}</p>`;
				contentArea.classList.remove("opacity-50", "scale-95");
			});
	}, 200);
}

/**
 * Sets up the sub-tabs for Interactions / Substitutes once "index.php" is loaded
 */
function initDrugInfoTabs() {
	const tabs = document.querySelectorAll(".tabs button");
	const tabContentContainer = document.getElementById("tab-content-container");
	if (!tabContentContainer) return;

	// Default load interactions.php
	loadSubTab("interactions.php");

	tabs.forEach((tab) => {
		tab.addEventListener("click", () => {
			tabContentContainer.classList.add("opacity-50", "scale-95");

			// Clear old active styles
			tabs.forEach((t) =>
				t.classList.remove("active", "border-blue-500", "text-blue-600")
			);
			tab.classList.add("active", "border-blue-500", "text-blue-600");

			const which =
				tab.id === "tab-interactions" ? "interactions.php" : "substitutes.php";
			setTimeout(() => loadSubTab(which), 200);
		});
	});

	function loadSubTab(subUrl) {
		fetch(subUrl)
			.then((res) => {
				if (!res.ok) {
					throw new Error(`Failed to load ${subUrl}`);
				}
				return res.text();
			})
			.then((html) => {
				tabContentContainer.innerHTML = html;

				// If we loaded interactions.php, call window.initializeInteractionSearch
				if (subUrl === "interactions.php") {
					if (window.initializeInteractionSearch) {
						window.initializeInteractionSearch();
						if (window.restoreInteractionState) {
							window.restoreInteractionState();
						}
					}
				}
				// If we loaded substitutes.php, call window.initializeSubstituteSearch
				else if (subUrl === "substitutes.php") {
					if (window.initializeSubstituteSearch) {
						window.initializeSubstituteSearch();
						if (window.restoreSubstituteState) {
							window.restoreSubstituteState();
						}
					}
				}

				tabContentContainer.classList.remove("opacity-50", "scale-95");
			})
			.catch((err) => {
				console.error("Error loading sub-tab:", err);
				tabContentContainer.innerHTML = `<p class="text-red-500">Failed to load ${subUrl}</p>`;
				tabContentContainer.classList.remove("opacity-50", "scale-95");
			});
	}
}
