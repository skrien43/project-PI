document.addEventListener("DOMContentLoaded", function () {
    if (sessionStorage.getItem("tpM") === "true") {
        const section = document.getElementById("teleportM");
        if (section) {
            section.scrollIntoView({ behavior: "instant" });
        }
        sessionStorage.removeItem("tpM");
    }
});
document.addEventListener("DOMContentLoaded", function () {
    if (sessionStorage.getItem("tpD") === "true") {
        const section = document.getElementById("teleportD");
        if (section) {
            section.scrollIntoView({ behavior: "instant" });
        }
        sessionStorage.removeItem("tpD");
    }
});


document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function () {
        sessionStorage.setItem('scrollX', window.scrollX);
        sessionStorage.setItem('scrollY', window.scrollY);
        const tableDiv = document.querySelector('div[style*="overflow-x: scroll"]');
        if (tableDiv) {
        sessionStorage.setItem('scrollLeftTable', tableDiv.scrollLeft);
        }
    });
});
window.addEventListener('load', () => {
    const scrollX = sessionStorage.getItem('scrollX');
    const scrollY = sessionStorage.getItem('scrollY');
    const scrollLeftTable = sessionStorage.getItem('scrollLeftTable');
    if (scrollX && scrollY) window.scrollTo(scrollX, scrollY);

    const tableDiv = document.querySelector('div[style*="overflow-x: scroll"]');
    if (tableDiv && scrollLeftTable) {
    tableDiv.scrollLeft = scrollLeftTable;
    }

    sessionStorage.removeItem('scrollX');
    sessionStorage.removeItem('scrollY');
    sessionStorage.removeItem('scrollLeftTable');
});


document.getElementById('ref')
    .addEventListener('click', function () {
        const scrollX = document.getElementById('refScr').scrollLeft;
        sessionStorage.setItem('tbM', scrollX);
    });
window.addEventListener('load', function () {
    const scrollX = sessionStorage.getItem('tbM');
    if (scrollX !== null) {
        document.getElementById('refScr').scrollLeft = parseInt(scrollX);
        sessionStorage.removeItem('tbM');
    }
});
