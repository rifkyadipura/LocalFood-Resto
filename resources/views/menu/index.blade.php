@extends('layouts.appDashboard')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg rounded-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top">
            <h4 class="mb-0">Daftar Menu</h4>
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('menu.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Menu Baru
                </a>
            @endif
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table id="menu-table" class="table table-striped table-hover table-bordered align-middle">
                    <thead class="table-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th>Status</th>
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
<script>
    $(document).ready(function () {
        $('#menu-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('menu.data') }}",  // Mengambil data dari rute DataTables
                error: function (xhr, error, thrown) {
                    console.error("AJAX Error:", xhr.responseText);
                }
            },
            order: [[0, 'desc']],  // Mengurutkan berdasarkan kolom pertama (No) secara menurun
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'harga', name: 'harga' },
                { data: 'stok', name: 'stok' },
                { data: 'kategori', name: 'kategori', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
