// js/blog.js
// This script fetches partial RSS items from server.php?type=fetchRSSBatch&start=X&count=Y
// and renders them in #rss-container, with pagination in #pagination-controls.
//
// Usage:
//  1) Standalone: the script automatically runs on DOMContentLoaded, calls loadPage(1).
//  2) Partial: call window.initializeBlogFeed() after the partial is in the DOM.

const feedUrl = "server.php"; // or the correct path to your server endpoint
let currentPage = 1;
const perPage = 10;
let totalItems = 0;
let totalPages = 1;

/**
 * If you're loading this script in a normal HTML page (like blog.php),
 * it will run automatically on DOMContentLoaded, loading page 1.
 */
document.addEventListener("DOMContentLoaded", () => {
	// If the page already has #rss-container, we can do:
	if (document.getElementById("rss-container")) {
		loadPage(1);
	}
});

/**
 * For partial usage (like "blog_page.php" loaded via fetch in a dashboard),
 * define a global function so you can call `window.initializeBlogFeed();`
 * once the partial is inserted into the DOM.
 */
window.initializeBlogFeed = function (page = 1) {
	loadPage(page);
};

/**
 * loadPage(page):
 *  - compute 'start'
 *  - fetch server.php?type=fetchRSSBatch&start=...&count=...
 *  - parse JSON -> items, total
 *  - call renderCards() + renderPagination()
 */
function loadPage(page) {
	currentPage = page;
	const start = (page - 1) * perPage;

	// OPTIONAL: If you want to update the browser's URL, uncomment:
	/*
  const newUrl = new URL(window.location.href);
  newUrl.searchParams.set("page", page);
  window.history.pushState({}, "", newUrl);
  */

	fetch(`${feedUrl}?type=fetchRSSBatch&start=${start}&count=${perPage}`)
		.then((resp) => resp.json())
		.then((data) => {
			if (data.error) {
				console.error("RSS error:", data.error);
				return;
			}
			// data should have { items, total }
			const items = data.items || [];
			totalItems = data.total || items.length;
			totalPages = Math.ceil(totalItems / perPage);

			renderCards(items);
			renderPagination();
		})
		.catch((err) => {
			console.error("Fetch error:", err);
		});
}

/**
 * Insert the list of items into #rss-container as "cards".
 */
function renderCards(items) {
	const container = document.getElementById("rss-container");
	if (!container) {
		console.warn("No #rss-container element found.");
		return;
	}
	container.innerHTML = "";

	items.forEach((item) => {
		const card = document.createElement("article");
		card.className = "bg-white rounded shadow p-4 flex flex-col mb-4";

		const title = sanitizeHTML(item.title || "");
		const pubDate = sanitizeHTML(item.pubDate || "");
		const link = sanitizeHTML(item.link || "#");
		const snippetText = truncateDescription(item.description || "", 120);

		card.innerHTML = `
      <h3 class="text-lg font-bold mb-2 line-clamp-2 hover:text-pink-600">
        ${title}
      </h3>
      <p class="text-xs text-gray-400 mb-1">${pubDate}</p>
      <p class="text-sm text-gray-700 flex-1">
        ${snippetText}
      </p>
      <div class="mt-3 text-right">
        <a href="${link}" target="_blank" rel="noopener"
           class="text-blue-600 text-sm hover:underline"
        >
          Read More
        </a>
      </div>
    `;
		container.appendChild(card);
	});
}

/**
 * Show pagination buttons in #pagination-controls as <button> elements.
 * Clicking them calls loadPage(...) without reloading the entire page.
 */
function renderPagination() {
	const controls = document.getElementById("pagination-controls");
	if (!controls) {
		console.warn("No #pagination-controls element found.");
		return;
	}
	controls.innerHTML = "";

	const maxLinks = 10;
	const startPage = Math.max(1, currentPage - 5);
	const endPage = Math.min(totalPages, startPage + maxLinks - 1);

	// Prev
	if (currentPage > 1) {
		const prevBtn = document.createElement("button");
		prevBtn.textContent = "Prev";
		prevBtn.className = "px-3 py-1 border rounded hover:bg-gray-100";
		prevBtn.addEventListener("click", () => loadPage(currentPage - 1));
		controls.appendChild(prevBtn);
	}

	// Page links
	for (let p = startPage; p <= endPage; p++) {
		const btn = document.createElement("button");
		btn.textContent = p;
		btn.className =
			p === currentPage
				? "bg-pink-500 text-white px-3 py-1 rounded"
				: "bg-white border px-3 py-1 rounded hover:bg-gray-100";

		btn.addEventListener("click", () => loadPage(p));
		controls.appendChild(btn);
	}

	// Next
	if (currentPage < totalPages) {
		const nextBtn = document.createElement("button");
		nextBtn.textContent = "Next";
		nextBtn.className = "px-3 py-1 border rounded hover:bg-gray-100";
		nextBtn.addEventListener("click", () => loadPage(currentPage + 1));
		controls.appendChild(nextBtn);
	}
}

/**
 * Remove HTML tags and truncate.
 */
function truncateDescription(html, maxLen) {
	const text = html.replace(/<[^>]*>/g, "");
	if (text.length > maxLen) {
		return text.slice(0, maxLen) + "...";
	}
	return text;
}

/**
 * Minimal sanitize (to avoid injection).
 */
function sanitizeHTML(str) {
	const temp = document.createElement("div");
	temp.textContent = str;
	return temp.innerHTML;
}

/**
 * If user hits "Back" or "Forward", we handle popstate to restore that page
 * (only relevant if you used history.pushState)
 */
window.addEventListener("popstate", () => {
	const urlParams = new URLSearchParams(window.location.search);
	const page = parseInt(urlParams.get("page") || "1", 10);
	loadPage(page);
});
