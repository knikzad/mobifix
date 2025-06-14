<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Customer Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<style>
    .sidebar .nav-link {
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        color: #000;
    }

    .sidebar .nav-link:hover {
        background-color: #d6d8db !important;
        color: #000 !important;
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link[aria-expanded="true"] {
        background-color: #adb5bd !important;
        font-weight: 500;
        border-left: 3px solid #0d6efd;
        color: #000 !important;
    }

    .sidebar .collapse .nav-link {
        padding-left: 2rem;
    }

    .sidebar .collapse .nav-link:hover {
        background-color: #ced4da !important;
        color: #000 !important;
    }

    .sidebar .collapse .nav-link.active {
        background-color: #adb5bd !important;
        color: #000 !important;
        font-weight: 500;
        border-left: 3px solid #0d6efd;
    }
</style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        {{-- Sidebar --}}
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse border-end">
            <div class="position-sticky pt-3">
                <h5 class="px-3 mb-3 text-primary fw-bold">
                    <a class="nav-link" href="#">
                        Mobifix Customer Panel
                    </a>
                </h5>
                <ul class="nav flex-column">

                    {{-- swtich to admin panel --}}
                    <li class="nav-item">
                        <a class="btn btn-secondary" href="{{ route('admin.home') }}">
                           <i class="bi bi-person-badge"></i>
                            Switch to Admin
                        </a>
                    </li>
                    <li class = "nav-title" style = "font-weight: bold;">SQL Part</li>
                    {{-- Repair Services --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('use_case.appointment.create') ? 'active' : '' }}"
                           href="{{ route('use_case.appointment.create') }}">
                             Appointment Use Case
                        </a>
                    </li>

                    {{-- My Appointments --}}
                    <!-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.appointments') ? 'active' : '' }}"
                           href="{{ route('customer.appointments') }}">
                            My Appointments
                        </a>
                    </li> -->

                    {{-- Mustafa --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mustafa.use_case.page') ? 'active' : '' }}"
                        href="{{ route('mustafa.use_case.page') }}">
                            Payment Use Case
                        </a>
                    </li>
                    <li class = "nav-title" style = "font-weight: bold;">NoSQL Part</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('nosql.use_case.index') ? 'active' : '' }}"
                        href="{{ route('nosql.use_case.index') }}">
                            Appointment Use Case
                        </a>
                    </li>

                    {{-- Mustafa NoSQL --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mustafa.nosql.use_case') ? 'active' : '' }}"
                        href="{{ route('mustafa.nosql.use_case') }}">
                           Payment Use Case
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        {{-- Main Content --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
