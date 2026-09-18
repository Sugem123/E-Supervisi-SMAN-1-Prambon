const skalaConfig = {
    1: { kategori: "Kurang", catatan: "Kurang,Tidak memiliki bukti dukung." },
    2: { kategori: "Cukup", catatan: "Cukup, Memiliki bukti dukung, tetapi belum lengkap." },
    3: { kategori: "Baik", catatan: "Baik, Memiliki bukti dukung yang lengkap, namun belum sepenuhnya sesuai." },
    4: { kategori: "Sangat Baik", catatan: "Sangat Baik,Memiliki bukti dukung yang lengkap dan sepenuhnya sesuai." }
};

// Track status setiap aspek penilaian
const aspekStates = new Map();

$(document).ready(function () {
    // Inisialisasi elemen-elemen yang dibutuhkan
    initializePenilaianElements();

    // Event delegation untuk dropdown skala
    $(document).on('change', '.skala-dropdown', handleSkalaChange);

    // Event delegation untuk input catatan
    $(document).on('input', '.catatan-field', handleCatatanInput);

    // Event delegation untuk perhitungan skor real-time
    $(document).on('change', '.skala-dropdown', calculateScores);

    // Hitung skor awal saat halaman dimuat
    calculateScores();

    // Handle form submission for each step
    $('form[id^="form-"]').on('submit', function (e) {
        e.preventDefault();

        var form = $(this);
        var formData = form.serialize();
        var stepId = form.attr('id').split('-')[1];
        var submitBtn = form.find('button[type="submit"]');

        // Disable submit button and show loading text
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        console.log('Sending data for step ' + stepId);

        var baseUrl = (typeof BASE_URL !== 'undefined') ? BASE_URL : '';
        $.ajax({
            url: baseUrl + '/kepala/penilaian/save',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                console.log('Success response:', response);
                if (response.status === 'success') {
                    showNotification('success', 'Berhasil', 'Data berhasil disimpan');
                    // Mark step as completed
                    $('#step-' + stepId + '-tab').find('.status-icon').remove();
                    $('#step-' + stepId + '-tab').append(' <span class="badge badge-success status-icon">✓</span>');

                    // Update CSRF token
                    if (response.csrf_token) {
                        $('input[name="csrf_test_name"]').val(response.csrf_token);
                    }
                } else {
                    showNotification('error', 'Gagal', 'Gagal menyimpan data: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);

                let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += ' ' + xhr.responseJSON.message;

                    // Handle CSRF Error specifically
                    if (xhr.responseJSON.message.includes('The action you requested is not allowed')) {
                        errorMessage = 'Sesi Anda telah berakhir atau token keamanan tidak valid. Silakan muat ulang halaman.';
                    }
                } else if (xhr.status === 403) {
                    errorMessage = 'Sesi Anda telah berakhir atau token keamanan tidak valid. Silakan muat ulang halaman.';
                }

                showNotification('error', 'Error', errorMessage);
            },
            complete: function () {
                // Re-enable submit button
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Tahap Ini');
            }
        });
    });

    // Handle bulk edit functionality
    $(document).on('click', '.bulk-edit-apply-btn', function () {
        var tabId = $(this).data('tab-id');
        var selectedScore = $(this).closest('.alert').find('.bulk-skor-select').val();

        if (!selectedScore) {
            showNotification('warning', 'Peringatan', 'Silakan pilih skor terlebih dahulu');
            return;
        }

        var activeTabPane = $('#step-' + tabId);

        activeTabPane.find('.skala-dropdown').each(function () {
            const dropdown = $(this);
            const aspekId = dropdown.data('aspek');
            const catatanField = activeTabPane.find('.catatan-field[data-aspek="' + aspekId + '"]');

            dropdown.val(selectedScore);

            if (skalaConfig[selectedScore]) {
                catatanField.val(skalaConfig[selectedScore].catatan);
                catatanField.removeClass('manual-catatan');
                aspekStates.set(aspekId, 'auto');
            }
        });

        // Recalculate scores after bulk update
        calculateScores();

        showNotification('success', 'Berhasil', 'Edit massal untuk tahap ini berhasil!');
    });

    // Handle final submission
    $('#completeBtn').on('click', function () {
        var jadwalId = $(this).data('jadwal-id');

        confirmAction(
            'Selesaikan Supervisi?',
            "Apakah Anda yakin ingin menyelesaikan supervisi ini? Setelah diselesaikan, data tidak dapat diubah.",
            'warning',
            'Ya, Selesaikan!',
            function () {
                var completeBtn = $('#completeBtn');
                completeBtn.prop('disabled', true).text('Memproses...');

        var baseUrl = (typeof BASE_URL !== 'undefined') ? BASE_URL : '';
                $.ajax({
                    url: baseUrl + '/kepala/penilaian/complete/' + jadwalId,
                    method: 'POST',
                    data: {
                        'csrf_test_name': $('input[name="csrf_test_name"]').val()
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            showNotification('success', 'Berhasil', 'Supervisi berhasil diselesaikan!');
                            setTimeout(function () {
                                window.location.href = response.redirect;
                            }, 1500);
                        } else {
                            showNotification('error', 'Gagal', 'Gagal menyelesaikan supervisi: ' + response.message);
                            completeBtn.prop('disabled', false).text('Selesaikan Supervisi');
                        }
                    },
                    error: function (xhr, status, error) {
                        let errorMessage = 'Terjadi kesalahan saat menyelesaikan supervisi: ' + error;

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                            if (xhr.responseJSON.message.includes('The action you requested is not allowed')) {
                                errorMessage = 'Sesi Anda telah berakhir atau token keamanan tidak valid. Silakan muat ulang halaman.';
                            }
                        } else if (xhr.status === 403) {
                            errorMessage = 'Sesi Anda telah berakhir atau token keamanan tidak valid. Silakan muat ulang halaman.';
                        }

                        showNotification('error', 'Error', errorMessage);
                        completeBtn.prop('disabled', false).text('Selesaikan Supervisi');
                    }
                });
            }
        );
    });

    // Navigation Buttons Logic
    $(document).on('click', '.btn-next', function () {
        var activeTab = $('.nav-tabs .active');
        var nextTab = activeTab.parent().next('li').find('a');
        if (nextTab.length > 0) {
            nextTab.tab('show');
            $('html, body').animate({ scrollTop: $('.nav-tabs').offset().top - 100 }, 500);
        }
    });

    $(document).on('click', '.btn-prev', function () {
        var activeTab = $('.nav-tabs .active');
        var prevTab = activeTab.parent().prev('li').find('a');
        if (prevTab.length > 0) {
            prevTab.tab('show');
            $('html, body').animate({ scrollTop: $('.nav-tabs').offset().top - 100 }, 500);
        }
    });
});

function initializePenilaianElements() {
    // Setup dropdown skala untuk semua aspek
    setupSkalaDropdowns();
}

// Event handler untuk dropdown skala
function handleSkalaChange() {
    const dropdown = $(this);
    const aspekId = dropdown.data('aspek');
    const selectedValue = dropdown.val();
    const catatanField = $('.catatan-field[data-aspek="' + aspekId + '"]');

    if (catatanField.length === 0) return;

    // Cek status catatan saat ini
    const isManual = aspekStates.get(aspekId) === 'manual';

    // Hanya auto-fill jika bukan manual input dan nilai terpilih valid
    if (!isManual && skalaConfig[selectedValue]) {
        catatanField.val(skalaConfig[selectedValue].catatan);
        aspekStates.set(aspekId, 'auto');

        // Tambahkan styling untuk auto-filled fields
        catatanField.removeClass('manual-catatan');
    }
}

// Event handler untuk detect manual input
function handleCatatanInput() {
    const field = $(this);
    const aspekId = field.data('aspek');
    const currentValue = field.val();
    const autoTexts = Object.values(skalaConfig).map(config => config.catatan);

    // Jika user mengedit dan tidak sama dengan auto-text manapun
    if (!autoTexts.includes(currentValue) && currentValue.trim() !== '') {
        aspekStates.set(aspekId, 'manual');
        field.addClass('manual-catatan'); // styling untuk manual input
    } else if (currentValue.trim() === '') {
        // Jika field dikosongkan, reset state
        aspekStates.delete(aspekId);
        field.removeClass('manual-catatan');
    }
}

// Fungsi untuk mengatur dropdown skala 1-4 untuk semua aspek
function setupSkalaDropdowns() {
    $('.skala-dropdown').each(function () {
        const dropdown = $(this);
        // Pastikan dropdown memiliki opsi 1-4
        if (dropdown.find('option[value="1"]').length === 0) {
            dropdown.append('<option value="1">1</option>');
        }
        if (dropdown.find('option[value="2"]').length === 0) {
            dropdown.append('<option value="2">2</option>');
        }
        if (dropdown.find('option[value="3"]').length === 0) {
            dropdown.append('<option value="3">3</option>');
        }
        if (dropdown.find('option[value="4"]').length === 0) {
            dropdown.append('<option value="4">4</option>');
        }
    });
}

// Fungsi untuk menghitung skor secara real-time
function calculateScores() {
    // Hitung total skor dari semua aspek
    let totalSkor = 0;
    let totalAspek = 0;

    $('.skala-dropdown').each(function () {
        const value = parseInt($(this).val());
        if (!isNaN(value) && value > 0) {
            totalSkor += value;
            totalAspek++;
        }
    });

    // Tampilkan hasil perhitungan
    $('#total-skor').text(totalSkor);

    // Hitung dan tampilkan nilai akhir: (totalSkor / totalMaksimal) * 100
    // totalMaksimal = totalAspek * 4 (karena skor maksimal per aspek adalah 4)
    const totalMaksimal = totalAspek * 4;
    const nilaiAkhir = totalMaksimal > 0 ? (totalSkor / totalMaksimal) * 100 : 0;
    $('#nilai-akhir').text(nilaiAkhir.toFixed(2));

    // Tentukan dan tampilkan kategori
    let kategori = '';
    if (nilaiAkhir >= 86) {
        kategori = 'Baik Sekali';
    } else if (nilaiAkhir >= 70) {
        kategori = 'Baik';
    } else if (nilaiAkhir >= 55) {
        kategori = 'Cukup';
    } else {
        kategori = 'Kurang';
    }
    $('#kategori-nilai').text(kategori);
}

// Helper function for notifications with fallback
function showNotification(type, title, message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: title,
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        // Fallback to standard alert
        alert(title + ': ' + message);
    }
}

// Helper function for confirmation dialog with fallback
function confirmAction(title, text, icon, confirmButtonText, callback) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    } else {
        if (confirm(text)) {
            callback();
        }
    }
}