@extends('layouts.appDashboard')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg rounded-lg border-0">
        <div class="card-header bg-primary text-white rounded-top">
            <h4 class="mb-0">Daftar Transaksi</h4>
        </div>
        <div class="card-body">
            <div class="mb-3 d-flex justify-content-end">
                <div>
                    <label for="filter-date-range" class="form-label mb-1">Filter Tanggal</label>
                    <input type="text" id="filter-date-range" class="form-control form-control-sm" placeholder="Pilih Rentang Tanggal" style="width: 250px;">
                </div>
                <button id="reporting-btn" class="btn btn-primary btn-sm align-self-end" style="margin-left: 8px;">
                    <i class="fas fa-chart-bar"></i> Reporting
                </button>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="reporting-modal" tabindex="-1" aria-labelledby="reporting-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reporting-modal-label">Laporan Menu Terjual</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Informasi tambahan seperti Menu Terlaris -->
                            <div id="reporting-content">
                                <p>Memuat data...</p>
                            </div>
                            <!-- Perubahan: Tabel DataTables -->
                            <div class="table-responsive mt-3">
                                <p><strong>Daftar Menu yang Terjual: </strong></p>
                                <table id="reporting-datatable" class="table table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Menu</th>
                                            <th>Jumlah Terjual</th>
                                            <th>Total (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <!-- Footer untuk total keseluruhan -->
                                    <tfoot>
                                        <tr>
                                            <th>Total Keseluruhan</th>
                                            <th id="total-jumlah"></th>
                                            <th id="total-harga"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="transaksi-table" class="table table-striped table-hover table-bordered align-middle">
                    <thead class="table-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>Kode Transaksi</th>
                            <th>Waktu Transaksi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Tambahkan Daterangepicker dari CDN -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
    $(document).ready(function () {
    const table = $('#transaksi-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('transaksi.data') }}",
            data: function (d) {
                d.start_date = $('#filter-date-range').data('daterangepicker')?.startDate?.format('YYYY-MM-DD') || '';
                d.end_date = $('#filter-date-range').data('daterangepicker')?.endDate?.format('YYYY-MM-DD') || '';
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode_transaksi', name: 'kode_transaksi' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ]
    });

    $('#filter-date-range').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
        }
    });

    $('#filter-date-range').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        table.ajax.reload();
    });

    $('#filter-date-range').on('cancel.daterangepicker', function () {
        $(this).val('');
        table.ajax.reload();
    });

    $('#reporting-btn').on('click', function () {
    const startDate = $('#filter-date-range').data('daterangepicker')?.startDate?.format('YYYY-MM-DD') || '';
    const endDate = $('#filter-date-range').data('daterangepicker')?.endDate?.format('YYYY-MM-DD') || '';

    $.ajax({
        url: "{{ route('transaksi.reporting') }}",
        method: "GET",
        data: { start_date: startDate, end_date: endDate },
        beforeSend: function () {
            $('#reporting-content').html('<p>Memuat data...</p>');
            $('#reporting-modal').modal('show');
        },
        success: function (response) {
            console.log('Data diterima:', response); // Debug respons dari server

            if (response.error) {
                $('#reporting-content').html('<p class="text-danger">Terjadi kesalahan: ' + response.error + '</p>');
                return;
            }

            // Hapus DataTables sebelumnya jika sudah ada
            if ($.fn.DataTable.isDataTable('#reporting-datatable')) {
                $('#reporting-datatable').DataTable().destroy();
            }

            // Inisialisasi DataTables
            $('#reporting-datatable').DataTable({
            data: response.data,
            columns: [
                { data: 'nama_menu' },
                { data: 'jumlah_terjual' },
                {
                    data: 'total_harga',
                    render: function (data) {
                        return `Rp${parseInt(data).toLocaleString('id-ID', { minimumFractionDigits: 2 })}`;
                    },
                },
            ],
            dom: 'lfrtip', // Show entries, search, pagination
            lengthMenu: [5, 10, 25, 50], // Opsi jumlah entri
            pageLength: 5, // Default entries per page
            footerCallback: function (row, data, start, end, display) {
                let totalJumlah = 0;
                let totalHarga = 0;

                // Hitung total jumlah dan total harga
                data.forEach(item => {
                    totalJumlah += parseInt(item.jumlah_terjual);
                    totalHarga += parseInt(item.total_harga);
                });

                // Tampilkan hasil di footer tabel
                $(row).find('#total-jumlah').html(totalJumlah);
                $(row).find('#total-harga').html(`Rp${totalHarga.toLocaleString('id-ID', { minimumFractionDigits: 2 })}`);
            },
        });

            // Tambahkan informasi lainnya di atas tabel
            let infoHtml = `<p><strong>Reporting dari Tanggal: ${startDate} hingga ${endDate}</strong></p>`;
            if (response.menu_terlaris) {
                infoHtml += `<p><strong>Menu Terlaris:</strong> ${response.menu_terlaris.nama_menu} (${response.menu_terlaris.jumlah_terjual} terjual)</p>`;
            }
            if (response.menu_tersedikit) {
                infoHtml += `<p><strong>Menu Tersedikit:</strong> ${response.menu_tersedikit.nama_menu} (${response.menu_tersedikit.jumlah_terjual} terjual)</p>`;
            }

            if (Object.keys(response.menu_belum_terjual).length > 0) {
                infoHtml += `<p><strong>Menu yang Belum Pernah Terjual:</strong></p><ul>`;
                for (const [kategori, menus] of Object.entries(response.menu_belum_terjual)) {
                    infoHtml += `<li><strong>${kategori}:</strong><ul>`;
                    menus.forEach(menu => {
                        infoHtml += `<li>${menu.nama_menu}</li>`;
                    });
                    infoHtml += `</ul></li>`;
                }
                infoHtml += `</ul>`;
            } else {
                infoHtml += `<p><strong>Menu yang Belum Pernah Terjual:</strong> Tidak ada menu yang belum terjual.</p>`;
            }

            $('#reporting-content').html(infoHtml);
        },
        error: function (xhr, status, error) {
            console.error('Error:', xhr.responseText);
            $('#reporting-content').html('<p class="text-danger">Gagal memuat data.</p>');
        },
    });
});


});
</script>
@endpush
