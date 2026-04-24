<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kelulusan SKADA')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/SMK LOGO.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            min-height: 100vh;
            background: #212529;
            color: white;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar.hide {
            transform: translateX(-100%);
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .main-content.full {
            margin-left: 0;
        }

        /* ===== TOPBAR MOBILE ===== */
        .topbar-mobile {
            display: none;
            background: #212529;
            color: white;
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        /* ===== OVERLAY ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1039;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* ===== NAV LINKS ===== */
        .nav-link {
            color: rgba(255, 255, 255, .75);
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, .1);
        }

        /* ===== CARDS ===== */
        .card-counter {
            border: none;
            border-radius: 10px;
            transition: transform 0.2s;
        }

        .card-counter:hover {
            transform: translateY(-5px);
        }

        .photo-preview {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #eee;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .topbar-mobile {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="sidebar p-3" id="sidebar">
        <div class="d-flex justify-content-between align-items-center mb-4 py-3 border-bottom">
            <h5 class="mb-0">Guru Panel</h5>
            <button class="btn btn-sm btn-outline-light d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <ul class="nav flex-column">
            {{-- Dashboard --}}
            <li class="nav-item mb-2">
                <a href="{{ route('guru.dashboard') }}"
                    class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>

            {{-- Data Siswa --}}
            <li class="nav-item mb-2">
                <a href="{{ route('guru.siswa.index') }}"
                    class="nav-link {{ request()->routeIs('guru.siswa.*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate me-2"></i> Data Siswa
                </a>
            </li>

            {{-- Foto Kenangan --}}
            <li class="nav-item mb-2">
                <a href="{{ route('guru.foto.index') }}"
                    class="nav-link {{ request()->routeIs('guru.foto.*') ? 'active' : '' }}">
                    <i class="fas fa-images me-2"></i> Foto Kenangan
                </a>
            </li>

            {{-- Logout --}}
            <li class="nav-item mt-4">
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <div class="main-content" id="mainContent">
        <div class="topbar-mobile">
            <button class="btn btn-sm btn-outline-light" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <span class="fw-bold">Guru Panel</span>
            <span style="width:32px;"></span>
        </div>

        <div class="p-4">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth < 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                document.getElementById('sidebarOverlay').classList.remove('show');
            }
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
            });
        @endif
    </script>
</body>

</html>
