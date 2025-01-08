// blog.js
// We'll do a minimal example of pagination, loading from server side.

const feedUrl = "../server.php"; // or your path
let currentPage = 1;
const perPage = 10;
let totalItems = 0;
let totalPages = 1;

document.addEventListener("DOMContentLoaded", () => {
	loadPage(1);
});

function loadPage(page) {
	currentPage = page;
	const start = (page - 1) * perPage;

	fetch(`${feedUrl}?type=fetchRSSBatch&start=${start}&count=${perPage}`)
		.then((resp) => resp.json())
		.then((data) => {
			if (data.error) {
				console.error("Error:", data.error);
				return;
			}
			totalItems = data.total;
			totalPages = Math.ceil(totalItems / perPage);

			renderCards(data.items);
			renderPagination();
		})
		.catch((err) => {
			console.error("Fetch error:", err);
		});
}

function renderCards(items) {
	const container = document.getElementById("rss-container");
	container.innerHTML = "";

	items.forEach((item) => {
		const card = document.createElement("article");
		card.className = "bg-white rounded shadow p-4 flex flex-col";

		card.innerHTML = `
      <h3 class="text-lg font-bold mb-2 line-clamp-2 hover:text-pink-600">
        ${item.title}
      </h3>
      <p class="text-xs text-gray-400 mb-1">${item.pubDate}</p>
      <p class="text-sm text-gray-700 flex-1">
        ${truncateDescription(item.description, 120)}
      </p>
      <div class="mt-3 text-right">
        <a href="${item.link}" target="_blank" rel="noopener"
           class="text-blue-600 text-sm hover:underline"
        >
          Read More
        </a>
      </div>
    `;

		container.appendChild(card);
	});
}

function renderPagination() {
	const controls = document.getElementById("pagination-controls");
	controls.innerHTML = "";

	// We'll only show up to 10 page links
	let maxPageLinks = 10;
	let startPage = Math.max(1, currentPage - 5);
	let endPage = Math.min(totalPages, startPage + maxPageLinks - 1);

	// "Prev" button if needed
	if (currentPage > 1) {
		const prevBtn = document.createElement("button");
		prevBtn.textContent = "Prev";
		prevBtn.className = "px-3 py-1 border rounded hover:bg-gray-100";
		prevBtn.addEventListener("click", () => loadPage(currentPage - 1));
		controls.appendChild(prevBtn);
	}

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

	// "Next" button
	if (currentPage < totalPages) {
		const nextBtn = document.createElement("button");
		nextBtn.textContent = "Next";
		nextBtn.className = "px-3 py-1 border rounded hover:bg-gray-100";
		nextBtn.addEventListener("click", () => loadPage(currentPage + 1));
		controls.appendChild(nextBtn);
	}
}

function truncateDescription(html, maxLen) {
	// remove HTML tags
	let text = html.replace(/<[^>]+>/g, "");
	if (text.length > maxLen) {
		text = text.slice(0, maxLen) + "...";
	}
	return text;
}
