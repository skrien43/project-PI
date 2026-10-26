document.addEventListener('DOMContentLoaded', function () {
    const isAdmin = document.body.dataset.role === "admin";

    if (isAdmin && window.innerWidth <= 1280.1) {
        const confirmLogout = confirm("TIDAK DIREKOMENDASIKAN membuka halaman ini\nmenggunakan perangkat mobile. LOGOUT sekarang!");
        
        if (confirmLogout) {
            window.location.href = 'logout.php';
        } else {
            //dark overlay
                const overlay = document.createElement('div');
                overlay.style.position = 'fixed';
                overlay.style.top = 0;
                overlay.style.left = 0;
                overlay.style.width = '100vw';
                overlay.style.height = '100vh';
                overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.85)';
                overlay.style.zIndex = 9999;
                overlay.style.display = 'flex';
                overlay.style.flexDirection = 'column';
                overlay.style.alignItems = 'center';
                overlay.style.justifyContent = 'start';
                overlay.style.paddingTop = '100px';
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden';
            //pesan
                const pesan = document.createElement('div');
                pesan.style.backgroundColor = '#dee2e6';
                pesan.style.color = 'black';
                pesan.style.padding = '20px';
                pesan.style.fontWeight = '600';
                pesan.style.borderRadius = '10px';
                pesan.style.maxWidth = '80%';
                pesan.style.height = '180px';
                pesan.style.display = 'flex';
                pesan.style.alignItems = 'center';
                pesan.style.justifyContent = 'center';
                pesan.style.textAlign = 'center';
                pesan.style.border = '5px solid yellow';
                pesan.innerText = "⚠️ AKSES DITOLAK ⚠️\nGunakan Laptop dan/atau Komputer Anda\nuntuk membuka halaman ini.\nTekan tombol Back/Kembali pada perangkat Anda.";
            overlay.appendChild(pesan);
            document.body.appendChild(overlay);
            document.body.style.overflow = 'hidden';
        }
    }
});
