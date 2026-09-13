// Konfigurasi DataTables untuk semua halaman (#dataTable)
$(document).ready(function () {
    if ($('#dataTable').length && !$.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable({
        order: [],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Semua']],
        autoWidth: false,
        stateSave: true,
        stateSaveCallback: function (settings, data) {
            localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(data));
        },
        stateLoadCallback: function (settings) {
            return JSON.parse(localStorage.getItem('DataTables_' + window.location.pathname) || 'null');
        },
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ baris',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total baris)',
            zeroRecords: 'Tidak ada data yang cocok.',
            emptyTable: 'Belum ada data.',
            paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
        }
    });
}
});