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
});
</script>
@endpush
