<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LocalFood') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"> </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icons.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}"> --}}
    <!-- endinject -->
    <!-- Plugin css for this page -->
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}" /> --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendors/jvectormap/jquery-jvectormap.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendors/daterangepicker/daterangepicker.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendors/chartist/chartist.min.css') }}"> --}}
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/vertical-light-layout/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
</head>

<body>
    <div>
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo" href="index.html">
                    <span class="logo-dark" style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: bold; color: #ffffff; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);">
                        LocalFood
                    </span>
                    <img src="{{ asset('assets/images/logo-light.svg') }}" alt="logo-light" class="logo-light">
                </a>
                <a class="navbar-brand brand-logo-mini" href="index.html">
                    <img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" />
                </a>
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="icon-menu"></span>
                </button>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center">
                <h5 class="mb-0 font-weight-medium d-none d-lg-flex">Welcome LocalFood dashboard!</h5>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown d-none d-xl-inline-flex user-dropdown">
                            <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                <img class="img-xs rounded-circle ms-2"
                                     src="{{ asset('assets/images/faces/face8.jpg') }}"
                                     alt="Profile image">
                                <span class="font-weight-normal"> {{ Auth::user()->full_name }} </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                                <div class="dropdown-header text-center">
                                    <img class="img-md rounded-circle"
                                         src="{{ asset('assets/images/faces/face8.jpg') }}"
                                         alt="Profile image">
                                    <p class="mb-1 mt-3">{{ Auth::user()->full_name }}</p>
                                    <p class="font-weight-light text-muted mb-0">{{ Auth::user()->email }}</p>
                                </div>
                                <a class="dropdown-item" href="#"><i class="dropdown-item-icon icon-user text-primary"></i> My Profile</a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="dropdown-item-icon icon-power text-primary"></i> Sign Out
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                    <span class="icon-menu"></span>
                </button>
            </div>
        </nav>

        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item navbar-brand-mini-wrapper">
                        <a class="nav-link navbar-brand brand-logo-mini" href="index.html">
                            <img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" />
                        </a>
                    </li>
                    <li class="nav-item nav-profile">
                        <a href="#" class="nav-link">
                            <div class="profile-image">
                                <img class="img-xs rounded-circle" src="{{ asset('assets/images/faces/face8.jpg') }}" alt="profile image">
                                <div class="dot-indicator bg-success"></div>
                            </div>
                            <div class="text-wrapper">
                                <p class="profile-name">{{ Auth::user()->full_name }}</p>
                                <p class="designation">{{ Auth::user()->role }}</p>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/home">
                            <span class="menu-title">Dashboard</span>
                            <i class="icon-screen-desktop menu-icon"></i>
                        </a>
                    </li>
                    @if (in_array(auth()->user()->role, ['admin', 'Head Staff']))
                        <li class="nav-item">
                            <a class="nav-link text-dark font-semibold" href="{{ route('users.index') }}">
                                <span class="menu-title">Users</span>
                                <i class="icon-user menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark font-semibold" href="{{ route('transaksi.index') }}">
                                <span class="menu-title">Transaksi</span>
                                <i class="icon-wallet menu-icon"></i>
                            </a>
                        </li>
                    @endif

                    @if (in_array(auth()->user()->role, ['admin', 'Head Staff', 'Cashier']))
                        <li class="nav-item">
                            <a class="nav-link text-dark font-semibold" href="{{ route('menu.index') }}">
                                <span class="menu-title">Menu</span>
                                <i class="icon-list menu-icon"></i>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->role === 'Cashier')
                        <li class="nav-item">
                            <a class="nav-link text-dark font-semibold" href="{{ route('index.pemesanan') }}">
                                <span class="menu-title">Pemesanan</span>
                                <i class="icon-basket-loaded menu-icon"></i>
                            </a>
                        </li>
                    @endif

                    <!-- Tambahkan My Profile & Sign Out di bagian bawah sidebar (hanya tampil di mobile) -->
                    <li class="nav-item d-block d-xl-none mt-3">
                        <hr class="sidebar-divider"> <!-- Divider -->
                    </li>
                    <li class="nav-item d-block d-xl-none">
                        <a class="nav-link" href="#">
                            <i class="icon-user menu-icon me-2"></i>
                            <span class="menu-title">My Profile</span>
                        </a>
                    </li>
                    <li class="nav-item d-block d-xl-none">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="icon-power menu-icon me-2"></i>
                            <span class="menu-title">Sign Out</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </nav>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <!-- Quick Action Toolbar Ends-->
                    <div class="content-wrapper">
                        <main class="py-4">
                            @yield('content')
                        </main>
                    </div>

                </div>
                <!-- content-wrapper ends -->

                <!-- Footer -->
                <footer class="footer">
                    <div style="min-height: 50vh;"></div>
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                            Copyright © {{ date('Y') }} <strong>Localood</strong>. All rights reserved.
                            <a href="#">Terms of Use</a> | <a href="#">Privacy Policy</a>
                        </span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">
                            Hand-crafted & made with <i class="icon-heart text-danger"></i> by <strong>Riky Najra Adipura</strong>
                        </span>
                    </div>
                </footer>
                <!-- Footer ends -->
            </div>
            <!-- main-panel ends -->
        </div>
    </div>
    <!-- Tempatkan stack script di sini -->
    @stack('scripts')

    <!-- plugins:js -->
    {{-- <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script> --}}
    <!-- endinject -->
    <!-- Plugin js for this page -->
    {{-- <script src="{{ asset('assets/vendors/chart.js/chart.umd.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendors/daterangepicker/daterangepicker.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendors/chartist/chartist.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendors/progressbar.js/progressbar.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script> --}}
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/settings.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/todolist.js') }}"></script> --}}
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <!-- End custom js for this page -->
</body>

</html>
