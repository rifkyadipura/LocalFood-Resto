@extends('layouts.appDashboard')

@section('content')
    {{-- <div class="container mt-5">
        <div class="card shadow-sm">
            <!-- Card Header -->
            <div class="card-header text-white text-center">
                {{ __('Dashboard') }}
            </div>

            <!-- Card Body -->
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h1 class="text-3xl font-weight-bold text-dark mb-3">
                        Hi, Selamat Datang {{ auth()->user()->name }}
                    </h1>
                    <p class="text-muted">
                        Saat ini Anda berada di halaman LocalFood.
                    </p>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="container mt-4">
        <div class="alert alert-info text-center">
            <h2 class="font-weight-bold">Selamat Datang di Dashboard LocalFood!</h2>
            <p class="text-muted">Kelola operasional restoran Anda dengan lebih mudah dan efisien. Pantau inventaris produk, transaksi, serta informasi penting lainnya dalam satu tempat.</p>
        </div>
    </div>
@endsection
