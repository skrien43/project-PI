document.getElementById('btnExpM').addEventListener('click',
    function (e) {
        const otpCells = document.querySelectorAll('#table-mhs tbody td:nth-child(11)');
        let Mdonent = false;
        otpCells.forEach(td => {
            if (td.textContent.trim() !== '------') {
                Mdonent = true;
            }
        });
        if (Mdonent) {
            e.preventDefault();
            alert('Selesaikan semua pinjaman sebelum melakukan ekspor data!');
            return;
        }
        exportMHS('xlsx');
    }
);
function exportMHS(type) {
    const rows = [];
    const table = document.querySelectorAll('#table-mhs tbody tr');
    table.forEach(tr => {
        const cells = tr.querySelectorAll('td');
        rows.push({
            No: cells[0].innerText,
            NPM: cells[1].innerText,
            Nama: cells[2].innerText,
            Dosen_Pengajar: cells[3].innerText,
            Kelas: cells[4].innerText,
            Ruangan: cells[5].innerText,
            Jam_Kuliah: cells[6].innerText,
            No_HP: cells[7].innerText,
            Alat: cells[8].innerText,
            Jaminan: cells[9].innerText,
            Tgl_Jam_Kembali: cells[11].innerText
        });
    });
    rows.reverse();
    rows.forEach((row, i) => {
        row.No = i + 1;
    });
    const ws = XLSX.utils.json_to_sheet(rows);
    ws['!cols'] = [
        { wch: 5 },  // no
        { wch: 12 }, // npm
        { wch: 35 }, // nama
        { wch: 25 }, // dosen
        { wch: 10 }, // kelas
        { wch: 10 }, // room
        { wch: 10 }, // jkul
        { wch: 15 }, // hp
        { wch: 37 }, // stf
        { wch: 10 }, // grt
        { wch: 18 }  // datetime
    ];
    const jKul = 'G';
    for (let i = 2; i <= rows.length + 1; i++) {
        const cellRef = jKul + i;
        if (ws[cellRef]) {
            ws[cellRef].z = '@';
            ws[cellRef].t = 's';
        }
    }
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'RekapPeminjaman_Agt2025');
    XLSX.writeFile(wb, 'Rekap_Peminjaman_MHS.' + type);
}


document.getElementById('btnExpD').addEventListener('click',
    function (e) {
        const otpCells = document.querySelectorAll('#table-dosen tbody td:nth-child(8)');
        let Ddonent = false;
        otpCells.forEach(td => {
            if (td.textContent.trim() !== '------') {
                Ddonent = true;
            }
        });
        if (Ddonent) {
            e.preventDefault();
            alert('Selesaikan semua pinjaman sebelum melakukan ekspor data!');
            return;
        }
        exportDOS('xlsx');
    }
);
function exportDOS(type) {
    const rows = [];
    const table = document.querySelectorAll('#table-dosen tbody tr');
    table.forEach(tr => {
        const cells = tr.querySelectorAll('td');
        rows.push({
            No: cells[0].innerText,
            Kode_Dosen: cells[1].innerText,
            Nama: cells[2].innerText,
            Ruangan: cells[3].innerText,
            Jam_Kuliah: cells[4].innerText,
            No_HP: cells[5].innerText,
            Alat: cells[6].innerText,
            Tgl_Jam_Kembali: cells[8].innerText
        });
    });
    rows.reverse();
    rows.forEach((row, i) => {
        row.No = i + 1;
    });
    const ws = XLSX.utils.json_to_sheet(rows);
    ws['!cols'] = [
        { wch: 5 },  //no
        { wch: 12 }, //kdDos
        { wch: 27 }, //nama
        { wch: 10 }, //room
        { wch: 10 }, //jkul
        { wch: 15 }, //hp
        { wch: 37 }, //stf
        { wch: 18 }  //datetime
    ];
    const jKul = 'E';
    for (let i = 2; i <= rows.length + 1; i++) {
        const cellRef = jKul + i;
        if (ws[cellRef]) {
            ws[cellRef].z = '@'; // force txt
            ws[cellRef].t = 's'; // txt
        }
    }
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'RekapPeminjaman_Agt2025');
    XLSX.writeFile(wb, 'Rekap_Peminjaman_DOSEN.' + type);
}
