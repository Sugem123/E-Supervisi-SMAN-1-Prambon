<div class="waktu-wib-container">
    <div class="waktu-wib-display">
        <span class="waktu-wib-text">Waktu Indonesia Barat (WIB)</span>
        <h2 class="waktu-wib-value" id="waktuWIB"><?= format_waktu_indonesia(date('Y-m-d H:i:s')) ?></h2>
        <p class="waktu-wib-date" id="tanggalWIB"><?= format_tanggal_indonesia(date('Y-m-d')) ?></p>
    </div>
</div>

<style>
.waktu-wib-container {
    text-align: center;
    padding: 20px;
    background-color: #f8f9fc;
    border-radius: 10px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    margin-bottom: 20px;
}

.waktu-wib-text {
    font-size: 14px;
    color: #858796;
    display: block;
    margin-bottom: 5px;
}

.waktu-wib-value {
    font-size: 28px;
    font-weight: bold;
    color: #4e73df;
    margin: 5px 0;
    font-family: 'Courier New', monospace;
}

.waktu-wib-date {
    font-size: 16px;
    color: #858796;
    margin: 0;
}

@media (max-width: 768px) {
    .waktu-wib-value {
        font-size: 24px;
    }
    
    .waktu-wib-date {
        font-size: 14px;
    }
}
</style>

<script>
// Fungsi untuk memperbarui waktu WIB setiap detik
function updateWaktuWIB() {
    const now = new Date();
    
    // Konversi ke zona waktu WIB (UTC+7)
    const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
    const wibTime = new Date(utc + (3600000 * 7));
    
    // Format waktu
    const hours = wibTime.getHours().toString().padStart(2, '0');
    const minutes = wibTime.getMinutes().toString().padStart(2, '0');
    const seconds = wibTime.getSeconds().toString().padStart(2, '0');
    
    // Format tanggal
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const dayName = days[wibTime.getDay()];
    const day = wibTime.getDate();
    const month = months[wibTime.getMonth() + 1];
    const year = wibTime.getFullYear();
    
    // Update elemen HTML
    document.getElementById('waktuWIB').textContent = `${hours}:${minutes}:${seconds} WIB`;
    document.getElementById('tanggalWIB').textContent = `${dayName}, ${day} ${month} ${year}`;
}

// Perbarui waktu setiap detik
setInterval(updateWaktuWIB, 1000);

// Jalankan sekali saat halaman dimuat
updateWaktuWIB();
</script>