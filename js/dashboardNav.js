// ./js/dashboardNav.js

document.addEventListener("DOMContentLoaded", () => {
	// 1) Hamburger logic for mobile sidebar
	const hamburgerButton = document.querySelector(
		"[data-drawer-toggle='logo-sidebar']"
	);
	const sidebar = document.getElementById("logo-sidebar");

	if (hamburgerButton && sidebar) {
		hamburgerButton.addEventListener("click", () => {
			// If you're also using Flowbite's own "Drawer" logic, remove or adapt this
			// otherwise, toggling "-translate-x-full" is enough to show/hide the sidebar
			sidebar.classList.toggle("-translate-x-full");
		});
	} else {
		console.warn("Hamburger or sidebar not found - cannot toggle sidebar.");
	}

	// 2) User menu logic
	const userMenuButton = document.querySelector(".user-menu-button");
	const userMenuDropdown = document.querySelector(".user-menu-dropdown");

	if (userMenuButton && userMenuDropdown) {
		// Toggle the dropdown on click
		userMenuButton.addEventListener("click", (evt) => {
			evt.stopPropagation();
			userMenuDropdown.classList.toggle("hidden");
		});

		// Close the dropdown if user clicks outside
		document.addEventListener("click", (evt) => {
			if (
				!userMenuButton.contains(evt.target) &&
				!userMenuDropdown.contains(evt.target)
			) {
				userMenuDropdown.classList.add("hidden");
			}
		});
	} else {
		console.warn(
			"User menu button or dropdown not found - cannot toggle user menu."
		);
	}
});
