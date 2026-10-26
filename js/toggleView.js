function toggleRows(link) {
    const section = link.closest('section');
    const rows = section.querySelectorAll('tbody tr');
    const showingAll = link.dataset.showing === 'true';
    let visibleCount = 0;

    rows.forEach(row => {
        if (row.id.startsWith("edit-row-")) return;
        if (showingAll) {
            if (visibleCount >= 10) {
                row.classList.add("hide");
            }
        } else {
            row.classList.remove("hide");
        }
        visibleCount++;
    });
    link.textContent = showingAll ? "view more..." : "view less...";
    link.dataset.showing = showingAll ? "false" : "true";

    if (showingAll) {
        section.scrollIntoView({
            behavior: 'instant',
            block: 'start'
        });
    }
}


section.scrollIntoView({ behavior: 'auto', block: 'start' });
const table = section.querySelector('table');
table.style.opacity = 0;
setTimeout(() => {
    table.style.transition = 'opacity 0.3s';
    table.style.opacity = 1;
}, 10);
