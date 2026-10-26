function searchBarang(type, query) {
    let resultsDiv = document.getElementById('results_' + type);
    if (query.length < 1) {
        resultsDiv.innerHTML = '';
        return;
    }
    fetch('search_barang.php?type=' + type + '&query=' + query)
        .then(response => response.json())
        .then(data => {
            resultsDiv.innerHTML = '';
            if (data.length > 0) {
                data.forEach(item => {
                    let div = document.createElement('div');
                    div.innerHTML = item.namaBarang + ' (' + item.kodeBarang + ')';
                    div.onclick = function() {
                        selectBarang(type, item.namaBarang, item.kodeBarang);
                    };
                    resultsDiv.appendChild(div);
                });
            } else {
                resultsDiv.innerHTML = '<div>Tidak ditemukan</div>';
            }
        });
}

function selectBarang(type, nama, kode) {
    document.getElementById('search_' + type).value = nama + ' (' + kode + ')';
    document.getElementById('kode_' + type).value = kode;
    document.getElementById('results_' + type).innerHTML = '';
}


document.addEventListener('click', function(event) {
    if (!event.target.closest('.i-w')) {
        document.querySelectorAll('.search-results').forEach(function(el) {
            el.innerHTML = '';
        });
    }
});
