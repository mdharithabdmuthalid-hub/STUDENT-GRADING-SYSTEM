const loginSuccess = document.getElementById('login-success');

if (loginSuccess) {
    window.setTimeout(() => loginSuccess.remove(), 5000);
}

const searchInput = document.getElementById('result-search');
const resultBody = document.getElementById('results-body');
const emptyState = document.getElementById('empty-state');
const resultCount = document.getElementById('result-count');

if (searchInput && resultBody && emptyState && resultCount) {
    const resultRows = Array.from(resultBody.querySelectorAll('tr'));

    function filterResults() {
        const query = searchInput.value.trim().toLocaleLowerCase();
        let visibleCount = 0;

        resultRows.forEach((row) => {
            const matches = row.textContent.toLocaleLowerCase().includes(query);
            row.hidden = !matches;
            if (matches) visibleCount++;
        });

        emptyState.hidden = visibleCount > 0;
        resultCount.textContent = `Showing ${visibleCount} of ${resultRows.length} results`;
    }

    searchInput.addEventListener('input', filterResults);
    filterResults();
}

const scoreFields = ['test1', 'test2', 'final_exam']
    .map((id) => document.getElementById(id))
    .filter(Boolean);
const totalPreview = document.getElementById('total-preview');

if (scoreFields.length > 0 && totalPreview) {
    function updateTotal() {
        const values = scoreFields.map((field) => field.value);
        totalPreview.textContent = values.every((value) => value !== '' && Number.isFinite(Number(value)))
            ? values.reduce((total, value) => total + Number(value), 0).toFixed(2)
            : '—';
    }

    scoreFields.forEach((field) => field.addEventListener('input', updateTotal));
}
