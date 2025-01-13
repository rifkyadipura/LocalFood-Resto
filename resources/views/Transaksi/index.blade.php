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
            <div class="modal fade" id="reporting-modal" tabindex="-1" aria-labelledby="reporting-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reporting-modal-label">Laporan Menu Terjual</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="reporting-content">
                                <p>Memuat data...</p>
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
                let html = `<p><strong>Reporting dari Tanggal: ${startDate} hingga ${endDate}</strong></p>`;

                if (response.menu_terlaris) {
                    html += `<p><strong>Menu Terlaris:</strong> ${response.menu_terlaris.nama_menu} (${response.menu_terlaris.jumlah_terjual} terjual)</p>`;
                }
                if (response.menu_tersedikit) {
                    html += `<p><strong>Menu Tersedikit:</strong> ${response.menu_tersedikit.nama_menu} (${response.menu_tersedikit.jumlah_terjual} terjual)</p>`;
                }

                html += '<table class="table table-striped table-hover">';
                html += '<thead><tr><th>Nama Menu</th><th>Jumlah Terjual</th><th>Total</th></tr></thead><tbody>';

                if (response.data.length > 0) {
                    response.data.forEach(item => {
                        html += `<tr>
                                    <td>${item.nama_menu}</td>
                                    <td>${item.jumlah_terjual}</td>
                                    <td>Rp${parseInt(item.total_harga).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</td>
                                </tr>`;
                    });
                } else {
                    html += '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>';
                }

                if (response.total_keseluruhan) {
                    html += `<tr>
                                <td><strong>Total Keseluruhan</strong></td>
                                <td><strong>${response.total_keseluruhan.jumlah_terjual}</strong></td>
                                <td><strong>Rp${parseInt(response.total_keseluruhan.total_harga).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</strong></td>
                            </tr>`;
                }

                html += '</tbody></table>';

                $('#reporting-content').html(html);
            },
            error: function () {
                $('#reporting-content').html('<p class="text-danger">Gagal memuat data.</p>');
            }
        });
    });
});
</script>
@endpush
