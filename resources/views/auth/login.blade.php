<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - SINTESA SDN PLAKPAK 1</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .portal-wrapper {
            max-width: 860px;
            width: 100%;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .portal-header {
            background-color: #195e38;
            border-bottom: 3px solid #b45309;
            color: #ffffff;
            padding: 22px 28px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .portal-header .logo-emblem {
            width: 50px;
            height: 50px;
            background: #ffffff;
            color: #195e38;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            flex-shrink: 0;
        }

        .portal-header .header-text h6 {
            margin: 0;
            font-size: 11px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #dcfce7;
            font-weight: 600;
        }

        .portal-header .header-text h4 {
            margin: 2px 0 0 0;
            font-size: 17px;
            font-weight: 700;
            color: #ffffff;
        }

        .portal-header .header-text small {
            font-size: 12px;
            color: #f0fdf4;
        }

        .portal-body {
            padding: 30px 32px;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #195e38;
            box-shadow: 0 0 0 3px rgba(25, 94, 56, 0.15);
        }

        .btn-submit {
            background-color: #195e38;
            border-color: #195e38;
            color: #ffffff;
            font-weight: 600;
            border-radius: 6px;
            padding: 10px;
            width: 100%;
            font-size: 14px;
        }

        .btn-submit:hover {
            background-color: #12472a;
            border-color: #12472a;
            color: #ffffff;
        }

        .quick-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
            cursor: pointer;
            text-align: left;
            transition: all 0.15s ease;
        }

        .quick-btn:hover {
            border-color: #195e38;
            background-color: #f0fdf4;
            transform: translateX(2px);
        }
    </style>
</head>
<body>

<div class="portal-wrapper">
    <!-- Header Resmi Sekolah -->
    <div class="portal-header">
        <div class="logo-emblem">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="header-text">
            <h6>Pemerintah Kabupaten Pamekasan &bull; Dinas Pendidikan</h6>
            <h4>SINTESA SDN PLAKPAK 1</h4>
            <small>SD Negeri Plakpak 1 Pegantenan &bull; Penilaian Siswa Berprestasi (BWM - WASPAS)</small>
        </div>
    </div>

    <!-- Body Login -->
    <div class="portal-body">
        <div class="row g-4">
            <!-- Form Login -->
            <div class="col-lg-7 border-end-lg pe-lg-4">
                <div class="mb-3">
                    <h5 class="fw-bold text-dark mb-1">Masuk ke Sistem</h5>
                    <p class="text-muted small mb-0">Masukkan username dan kata sandi Anda</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->has('username'))
                    <div class="alert alert-danger border-0 border-start border-danger border-4 py-2 px-3 small mb-3">
                        <div class="fw-bold"><i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first('username') }}</div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" id="mainLoginForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Username atau Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" id="usernameInput" class="form-control" value="{{ old('username') }}" placeholder="admin / walikelas3 / kepsek" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Kata Sandi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Masukkan kata sandi" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input" checked>
                        <label for="remember" class="form-check-label text-muted small">Ingat saya di perangkat ini</label>
                    </div>

                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sistem Sekarang
                    </button>
                </form>
            </div>

            <!-- Panel Bantuan & 1-Klik Masuk Langsung -->
            <div class="col-lg-5 ps-lg-4">
                <div class="mb-3">
                    <span class="badge bg-light text-dark border mb-2 fw-semibold">Bantuan Masuk Cepat</span>
                    <h6 class="fw-bold text-dark mb-1">Pilihan Akun Resmi</h6>
                    <p class="text-muted small mb-0">Klik tombol di bawah untuk langsung masuk sesuai peran:</p>
                </div>

                <div class="d-flex flex-column gap-2 mb-3">
                    <div class="quick-btn" onclick="directLogin('admin', 'admin123')">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark" style="font-size: 13px;">1. Administrator (TU)</span>
                            <span class="badge bg-light text-dark border">Masuk &rarr;</span>
                        </div>
                        <div class="text-muted small font-monospace mt-1">Pengguna: <strong>admin</strong> | Sandi: <strong>admin123</strong></div>
                    </div>

                    <div class="quick-btn" onclick="directLogin('walikelas3', 'wali123')">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark" style="font-size: 13px;">2. Wali Kelas (Kelas 6)</span>
                            <span class="badge bg-light text-dark border">Masuk &rarr;</span>
                        </div>
                        <div class="text-muted small font-monospace mt-1">Pengguna: <strong>walikelas3</strong> | Sandi: <strong>wali123</strong></div>
                    </div>

                    <div class="quick-btn" onclick="directLogin('kepsek', 'kepsek123')">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark" style="font-size: 13px;">3. Kepala Sekolah</span>
                            <span class="badge bg-light text-dark border">Masuk &rarr;</span>
                        </div>
                        <div class="text-muted small font-monospace mt-1">Pengguna: <strong>kepsek</strong> | Sandi: <strong>kepsek123</strong></div>
                    </div>
                </div>

                <div class="p-2 px-3 bg-light rounded border text-muted small" style="font-size: 11px;">
                    <i class="bi bi-info-circle me-1 text-primary"></i> 
                    Sistem fleksibel dan dapat menerima nama pengguna dalam huruf kecil maupun huruf besar, serta kata sandi peran Anda.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function directLogin(u, p) {
    document.getElementById('usernameInput').value = u;
    document.getElementById('passwordInput').value = p;
    document.getElementById('mainLoginForm').submit();
}

function togglePasswordVisibility() {
    const pwdInput = document.getElementById('passwordInput');
    const icon = document.getElementById('togglePasswordIcon');
    if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        pwdInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
