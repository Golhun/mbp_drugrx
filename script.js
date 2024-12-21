document.getElementById('submit-button').addEventListener('click', async function () {
    const drugsInput = document.getElementById('drugs').value.trim();
    const resultsContainer = document.getElementById('results');
    const loadingIndicator = document.getElementById('loading');

    if (!drugsInput) {
        alert('Please enter at least one drug name.');
        return;
    }

    // Show the loading skeleton
    resultsContainer.innerHTML = '';
    loadingIndicator.classList.remove('hidden');

    try {
        const response = await fetch('server.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ drugs: drugsInput }),
        });

        if (!response.ok) {
            throw new Error('Failed to fetch data from the server.');
        }

        const data = await response.json();

        // Hide the loading skeleton
        loadingIndicator.classList.add('hidden');

        // Render results
        resultsContainer.innerHTML = '';
        Object.entries(data).forEach(([drug, info]) => {
            const card = document.createElement('div');
            card.classList.add('p-4', 'bg-white', 'shadow-md', 'rounded-md', 'border', 'hover:shadow-lg', 'transition-shadow', 'space-y-4');

            const warnings = info.warnings || 'No warnings available';
            const interactions = info.interactions || 'No interaction data available';

            const severityClass = warnings.includes('severe')
                ? 'bg-red-50 border-l-4 border-red-500'
                : warnings.includes('moderate')
                ? 'bg-yellow-50 border-l-4 border-yellow-500'
                : 'bg-green-50 border-l-4 border-green-500';

            card.innerHTML = `
                <h3 class="text-xl font-bold text-blue-600 border-b pb-2">${drug}</h3>
                <div class="p-3 rounded-md ${severityClass}">
                    <h4 class="text-lg font-semibold">Warnings</h4>
                    <p class="text-gray-700 mt-1 truncate">${warnings}</p>
                    ${warnings.length > 150 ? '<button class="text-blue-500 mt-2 toggle-button">Show More</button>' : ''}
                </div>
                <div class="p-3 rounded-md bg-yellow-50 border-l-4 border-yellow-500">
                    <h4 class="text-lg font-semibold">Interactions</h4>
                    <p class="text-gray-700 mt-1">${interactions}</p>
                </div>
            `;

            resultsContainer.appendChild(card);
        });

        // Add toggle functionality for long warnings
        document.querySelectorAll('.toggle-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const paragraph = e.target.previousElementSibling;
                paragraph.classList.toggle('truncate');
                button.textContent = paragraph.classList.contains('truncate') ? 'Show More' : 'Show Less';
            });
        });
    } catch (error) {
        loadingIndicator.classList.add('hidden');
        resultsContainer.innerHTML = `<p class="text-red-500 text-center">Error: ${error.message}</p>`;
    }
});

