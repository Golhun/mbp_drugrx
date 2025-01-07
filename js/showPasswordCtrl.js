// ./js/showPasswordCtrl.js
// Allows user to hold the "Control" key to temporarily show password fields as plain text.

(function () {
	// Track if we're currently showing passwords or not
	let showingPasswords = false;

	// Event: Key Down
	document.addEventListener("keydown", (e) => {
		// If the user presses Control (either left or right)
		if (e.key === "Control" && !showingPasswords) {
			showingPasswords = true;
			// Switch all password fields to type="text"
			const passwordFields = document.querySelectorAll(
				'input[type="password"]'
			);
			passwordFields.forEach((field) => {
				field.dataset.originalType = "password"; // Store original type
				field.type = "text";
			});
		}
	});

	// Event: Key Up
	document.addEventListener("keyup", (e) => {
		// If the user releases Control
		if (e.key === "Control" && showingPasswords) {
			showingPasswords = false;
			// Revert all fields that were password to type="password"
			const textFields = document.querySelectorAll(
				'input[data-original-type="password"]'
			);
			textFields.forEach((field) => {
				field.type = "password";
				field.removeAttribute("data-original-type");
			});
		}
	});
})();
