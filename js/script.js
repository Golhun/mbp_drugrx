import { initializeTabSwitching } from "./tabSwitch.js";
import { initializeInteractionSearch } from "./interaction.js";
import { initializeSubstituteSearch } from "./substitute.js";

document.addEventListener("DOMContentLoaded", () => {
	initializeTabSwitching();
	initializeInteractionSearch();
	initializeSubstituteSearch();
});
