<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'SINTESA') - SINTESA SDN PLAKPAK 1</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root {
            /* Tema Resmi Instansi Sekolah Dasar Negeri (Hijau Kementerian Pendidikan & Emas Prestasi) */
            --instansi-primary: #195e38;
            --instansi-dark: #0f3d24;
            --instansi-hover: #12472a;
            --instansi-gold: #b45309;
            --instansi-gold-light: #fef3c7;
            --sidebar-width: 260px;
            --bg-canvas: #f1f5f9;
            --border-color: #cbd5e1;
            --border-light: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-size: 14px;
        }

        /* Sidebar Resmi Instansi */
        .sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.25s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand-box {
            padding: 16px 18px;
            background: var(--instansi-primary);
            color: #ffffff;
            border-bottom: 2px solid var(--instansi-gold);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand-box .school-logo {
            width: 40px;
            height: 40px;
            background: #ffffff;
            color: var(--instansi-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .sidebar-brand-box .brand-text {
            line-height: 1.25;
        }

        .sidebar-brand-box .brand-title {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
            color: #ffffff;
        }

        .sidebar-brand-box .brand-subtitle {
            font-size: 11px;
            color: #dcfce7;
            margin: 0;
            font-weight: 500;
        }

        /* Menu Navigasi */
        .sidebar-menu {
            padding: 14px 10px;
            flex: 1;
        }

        .sidebar-category {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
            padding: 14px 12px 6px 12px;
        }

        .sidebar-menu .nav-link {
            color: #334155;
            font-weight: 500;
            padding: 9px 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 3px;
            transition: all 0.15s ease;
        }

        .sidebar-menu .nav-link i {
            font-size: 16px;
            width: 22px;
            text-align: center;
            color: #64748b;
        }

        .sidebar-menu .nav-link:hover {
            background-color: #f1f5f9;
            color: var(--instansi-primary);
        }

        .sidebar-menu .nav-link:hover i {
            color: var(--instansi-primary);
        }

        .sidebar-menu .nav-link.active {
            background-color: #e6f4ea;
            color: var(--instansi-primary);
            font-weight: 700;
            border-left: 4px solid var(--instansi-primary);
        }

        .sidebar-menu .nav-link.active i {
            color: var(--instansi-primary);
        }

        .sidebar-footer {
            padding: 12px 14px;
            border-top: 1px solid var(--border-light);
            background: #fafafa;
        }

        /* Main Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            flex: 1;
        }

        /* Top Bar Instansi */
        .top-navbar {
            height: 64px;
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header-title-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .instansi-badge {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
        }

        /* Content Body */
        .content-body {
            padding: 22px 24px;
            flex: 1;
        }

        /* Kartu & Elemen Tampilan */
        .card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            margin-bottom: 20px;
        }

        .card-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            padding: 12px 18px;
            font-weight: 700;
            color: #0f172a;
            font-size: 14px;
            border-top-left-radius: 7px !important;
            border-top-right-radius: 7px !important;
        }

        .card-body {
            padding: 18px;
        }

        /* Kotak Ringkasan / KPI Box */
        .kpi-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .kpi-value {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .kpi-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            margin-top: 3px;
        }

        /* Tombol Resmi */
        .btn {
            font-weight: 600;
            border-radius: 6px;
            font-size: 13px;
            padding: 7px 14px;
        }

        .btn-primary {
            background-color: var(--instansi-primary);
            border-color: var(--instansi-primary);
            color: #ffffff;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--instansi-hover);
            border-color: var(--instansi-hover);
            color: #ffffff;
        }

        .btn-outline-primary {
            color: var(--instansi-primary);
            border-color: var(--instansi-primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--instansi-primary);
            color: #ffffff;
        }

        /* Tabel Resmi Instansi */
        .table {
            font-size: 13px;
            margin-bottom: 0;
        }

        .table > thead th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            border-bottom: 1px solid var(--border-color);
            padding: 10px 14px;
            vertical-align: middle;
        }

        .table > tbody td {
            padding: 10px 14px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-light);
        }

        .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: #fafbfc;
        }

        .table-hover > tbody > tr:hover > * {
            background-color: #f0fdf4;
        }

        /* Footer */
        footer {
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 14px 24px;
            font-size: 12px;
            color: #64748b;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media (max-width: 992px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Instansi -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand / Identitas Sekolah -->
        <div class="sidebar-brand-box">
            <div class="school-logo">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div class="brand-text">
                <div class="brand-title">SINTESA SDN PLAKPAK 1</div>
                <div class="brand-subtitle">SD Negeri Plakpak 1 Pegantenan</div>
            </div>
        </div>

        <!-- Menu Navigasi Sesuai Role -->
        <div class="sidebar-menu">
            @php $user = Auth::user(); @endphp

            @if($user && $user->role === 'admin')
                <div class="sidebar-category">Menu Administrator</div>
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dasbor Administrator
                </a>

                <div class="sidebar-category">Data Master</div>
                <a class="nav-link {{ request()->routeIs('admin.kriteria.*') ? 'active' : '' }}" href="{{ route('admin.kriteria.index') }}">
                    <i class="bi bi-list-check"></i> Kriteria Penilaian
                </a>
                <a class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}" href="{{ route('admin.siswa.index') }}">
                    <i class="bi bi-person-lines-fill"></i> Data Siswa (Kandidat)
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i> Manajemen Akun
                </a>

                <div class="sidebar-category">Kalkulasi Penilaian</div>
                <a class="nav-link {{ request()->routeIs('admin.bwm.*') ? 'active' : '' }}" href="{{ route('admin.bwm.index') }}">
                    <i class="bi bi-sliders"></i> Pembobotan BWM
                </a>
                <a class="nav-link {{ request()->routeIs('admin.waspas.*') ? 'active' : '' }}" href="{{ route('admin.waspas.index') }}">
                    <i class="bi bi-calculator"></i> Perankingan WASPAS
                </a>

                <div class="sidebar-category">Dokumen & Rekap</div>
                <a class="nav-link {{ request()->routeIs('admin.laporan.per_kelas') ? 'active' : '' }}" href="{{ route('admin.laporan.per_kelas') }}">
                    <i class="bi bi-award"></i> Rekap Juara per Kelas
                </a>
                <a class="nav-link {{ request()->routeIs('admin.laporan.index') ? 'active' : '' }}" href="{{ route('admin.laporan.index') }}">
                    <i class="bi bi-printer"></i> Cetak & Ekspor Laporan
                </a>
            @endif

            @if($user && $user->role === 'walikelas')
                <div class="sidebar-category">Menu Wali Kelas</div>
                <a class="nav-link {{ request()->routeIs('walikelas.dashboard') ? 'active' : '' }}" href="{{ route('walikelas.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dasbor Wali Kelas
                </a>
                <a class="nav-link {{ request()->routeIs('walikelas.penilaian.index') || request()->routeIs('walikelas.penilaian.edit') ? 'active' : '' }}" href="{{ route('walikelas.penilaian.index') }}">
                    <i class="bi bi-pencil-square"></i> Input Nilai Siswa
                </a>
                <a class="nav-link {{ request()->routeIs('walikelas.penilaian.matriks') ? 'active' : '' }}" href="{{ route('walikelas.penilaian.matriks') }}">
                    <i class="bi bi-table"></i> Formulir Matriks Kelas
                </a>
                <a class="nav-link {{ request()->routeIs('walikelas.penilaian.rekap') ? 'active' : '' }}" href="{{ route('walikelas.penilaian.rekap') }}">
                    <i class="bi bi-card-checklist"></i> Rekap Nilai Mentah
                </a>
            @endif

            @if($user && $user->role === 'kepsek')
                <div class="sidebar-category">Kepala Sekolah (Pengambil Keputusan)</div>
                <a class="nav-link {{ request()->routeIs('kepsek.dashboard') ? 'active' : '' }}" href="{{ route('kepsek.dashboard') }}">
                    <i class="bi bi-bar-chart"></i> Dasbor Pimpinan
                </a>
                <a class="nav-link {{ request()->routeIs('kepsek.verifikasi.index') ? 'active' : '' }}" href="{{ route('kepsek.verifikasi.index') }}">
                    <i class="bi bi-patch-check"></i> Verifikasi & Pengesahan
                </a>
                <a class="nav-link {{ request()->routeIs('kepsek.verifikasi.sk') ? 'active' : '' }}" href="{{ route('kepsek.verifikasi.sk') }}">
                    <i class="bi bi-file-earmark-text"></i> Surat Keputusan (SK)
                </a>
            @endif
        </div>

        <!-- Sidebar Footer / Logout -->
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="header-title-box">
                <button class="btn btn-sm btn-light border d-lg-none" id="toggleSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-md-block">
                    <div class="text-muted small fw-medium" style="font-size: 11px;">
                        PEMERINTAH KABUPATEN PAMEKASAN &bull; DINAS PENDIDIKAN DAN KEBUDAYAAN
                    </div>
                    <div class="fw-bold text-dark" style="font-size: 14px;">
                        SD NEGERI PLAKPAK 1 PEGANTENAN
                    </div>
                </div>
            </div>

            <!-- Identitas User Login -->
            <div class="d-flex align-items-center gap-3">
                @if(Auth::check())
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-bold text-dark" style="font-size: 13px;">{{ Auth::user()->nama_pengguna }}</div>
                        <div>
                            @if(Auth::user()->role === 'admin')
                                <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 10px;">Staf Tata Usaha</span>
                            @elseif(Auth::user()->role === 'walikelas')
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 10px;">Wali {{ Auth::user()->kelas_binaan ?? 'Kelas' }}</span>
                            @elseif(Auth::user()->role === 'kepsek')
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle" style="font-size: 10px;">Kepala Sekolah</span>
                            @endif
                        </div>
                    </div>
                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center fw-bold text-dark" style="width: 38px; height: 38px; font-size: 14px;">
                        {{ strtoupper(substr(Auth::user()->nama_pengguna, 0, 1)) }}
                    </div>
                @endif
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="content-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 border-0 border-start border-success border-4 py-2 px-3 mb-3" role="alert">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    <div><strong>Berhasil:</strong> {{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 border-0 border-start border-danger border-4 py-2 px-3 mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                    <div><strong>Perhatian:</strong> {{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 border-start border-danger border-4 py-2 px-3 mb-3" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-1"></i>Periksa input berikut:</div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer Instansi -->
        <footer>
            <div>
                &copy; {{ date('Y') }} <strong>SINTESA SDN PLAKPAK 1</strong> &bull; Sistem Penilaian Siswa Berprestasi (BWM - WASPAS)
            </div>
            <div class="text-muted">
                Kecamatan Pegantenan, Kabupaten Pamekasan
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('toggleSidebar')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
    @stack('scripts')
</body>
</html>
