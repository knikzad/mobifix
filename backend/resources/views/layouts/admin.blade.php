<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Ensure high specificity by targeting .sidebar .nav-link directly */
    .sidebar .nav-link {
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        color: #000;
    }

    .sidebar .nav-link:hover {
        background-color: #d6d8db !important;  /* Light gray with !important to override Bootstrap */
        color: #000 !important;
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link[aria-expanded="true"] {
        background-color: #adb5bd !important; /* Darker gray */
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
                    <a class="nav-link" href="{{ route('admin.home') }}">
                        Mobifix Admin Panel
                    </a>
                </h5>
                <ul class="nav flex-column">
                    <li class = "nav-title" style = "font-weight: bold;">SQL Part</li>
                    {{-- Employees --}}
                    <!-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}"
                           href="{{ route('admin.employees.index') }}">
                            Employees
                        </a>
                    </li> -->

                    {{-- Customers --}}
                    <!-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                           href="{{ route('admin.customers.index') }}">
                            Customers
                        </a>
                    </li> -->

                    {{-- Repair Orders --}}
                    <!-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.repair-orders.*') ? 'active' : '' }}"
                           href="{{ route('admin.repair-orders.index') }}">
                            Repair Orders
                        </a>
                    </li> -->

                    {{-- Devices Section --}}
                    <!-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('device.types*') ? 'active' : '' }}"
                            href="{{ route('device.types.index') }}">
                            Device Types
                        </a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('use_case.index') ? 'active' : '' }}"
                        href="{{ route('use_case.index') }}">
                            Appointment Use Case
                        </a>
                    </li>
                    {{-- Analytics --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('use_case.analytics.report') ? 'active' : '' }}"
                        href="{{ route('use_case.analytics.report') }}">
                            Appointment Analytics Report
                        </a>
                    </li>


                    {{-- Mustafa --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mustafa.use_case.page') ? 'active' : '' }}"
                        href="{{ route('mustafa.use_case.page') }}">
                            Payment Use Case
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mustafa.analytics.report') ? 'active' : '' }}"
                        href="{{ route('mustafa.analytics.report') }}">
                            Payment Analytics Report
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mustafa.pendingPayments') ? 'active' : '' }}"
                        href="{{ route('mustafa.pendingPayments') }}">
                            Pending Payments
                        </a>
                    </li>

                    <li class = "nav-title" style = "font-weight: bold;">NoSQL Part</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('use_case.index') ? 'active' : '' }}"
                        href="{{ route('nosql.use_case.index') }}">
                            Appointment Use Case
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('nosql.use_case.analytics.report') ? 'active' : '' }}"
                        href="{{ route('nosql.use_case.analytics.report') }}">
                            Appointment Analytics Report
                        </a>
                    </li>

                    {{-- Mustafa NoSQL --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mustafa.nosql.use_case') ? 'active' : '' }}"
                        href="{{ route('mustafa.nosql.use_case') }}">
                           Payment Use Case
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mustafa.nosql.analytics') ? 'active' : '' }}"
                        href="{{ route('mustafa.nosql.analytics') }}">
                            Payment Analytics Report
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
<!-- JS Script -->
<script>
document.getElementById('import-btn').addEventListener('click', async () => {
    const importBtn = document.getElementById('import-btn');
    const alertContainer = document.getElementById('alert-container');
    alertContainer.innerHTML = '';

    // Save original button content so we can restore it later
    const originalBtnContent = importBtn.innerHTML;

    try {
        // Show loading spinner & disable button
        importBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Loading...
        `;
        importBtn.disabled = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const resp = await fetch('/import-random-data', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        });

        const json = await resp.json();

        if (!resp.ok) throw new Error(json.message || 'Something went wrong.');

        alertContainer.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ${json.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
    } catch (err) {
        alertContainer.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Error importing data. Please try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        console.error(err);
    } finally {
        // Restore button content and enable button
        importBtn.innerHTML = originalBtnContent;
        importBtn.disabled = false;
    }
});
</script>

</body>
</html>
