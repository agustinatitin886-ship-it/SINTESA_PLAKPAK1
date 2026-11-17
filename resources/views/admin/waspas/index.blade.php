@extends('layouts.app')

@section('title', 'Kalkulator WASPAS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kalkulator WASPAS</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Perankingan Siswa Metode WASPAS</h4>
    </div>
    <div>
        @if($approval && $approval->status === 'disetujui')
            <span class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3">
                <i class="bi bi-patch-check-fill me-1"></i> Disahkan Kepala Sekolah (SK: {{ $approval->nomor_sk ?? '-' }})
            </span>
        @elseif($approval && $approval->status === 'ditolak')
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-2 px-3">
                <i class="bi bi-x-circle-fill me-1"></i> Status: Perlu Revisi / Catatan Kepsek
            </span>
        @elseif($savedResults > 0)
            <span class="badge bg-warning-subtle text-dark border border-warning-subtle py-2 px-3">
                <i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi & SK Kepsek
            </span>
        @endif
    </div>
</div>

<!-- Filter & Parameter Toolbar -->
<div class="card mb-4">
    <div class="card-header">
        Filter Data & Parameter Agregasi WASPAS
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.waspas.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary">Tahun Ajaran / Periode</label>
                <select name="periode" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($daftarPeriode as $p)
                        <option value="{{ $p }}" {{ $selectedPeriode == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary">Pilih Kelas</label>
                <select name="kelas" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">-- Semua Kelas (Gabungan) --</option>
                    @foreach($daftarKelas as $k)
                        <option value="{{ $k }}" {{ $selectedKelas == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small fw-semibold text-secondary mb-0">Parameter Nilai Lambda (&lambda;):</label>
                    <span class="badge bg-light text-dark border font-monospace" id="lambdaValText">{{ $lambda }}</span>
                </div>
                <input type="range" name="lambda" id="lambdaRange" min="0" max="1" step="0.05" value="{{ $lambda }}" class="form-range" oninput="document.getElementById('lambdaValText').innerText = this.value" onchange="this.form.submit()">
                <div class="d-flex justify-content-between text-muted" style="font-size: 10px;">
                    <span>&lambda;=0 (WPM)</span>
                    <span class="fw-bold">&lambda;=0.5 (Standar)</span>
                    <span>&lambda;=1 (WSM)</span>
                </div>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-arrow-clockwise me-1"></i> Hitung Ulang
                </button>
            </div>
        </form>
    </div>
</div>

@if($error)
    <div class="alert alert-warning border d-flex align-items-center gap-3">
        <i class="bi bi-exclamation-triangle-fill fs-4 text-warning"></i>
        <div>
            <strong>Pemberitahuan:</strong> {{ $error }}
            <div class="mt-2">
                <a href="{{ route('admin.bwm.index') }}" class="btn btn-sm btn-primary">Buka Kalkulator BWM</a>
            </div>
        </div>
    </div>
@elseif($calculation)

    <!-- Tabel Hasil Akhir Perankingan WASPAS -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Hasil Akhir Perankingan Siswa (Periode {{ $selectedPeriode }})</span>
            <span class="small text-muted">Jumlah Siswa: <strong>{{ count($calculation['results']) }}</strong></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">Peringkat</th>
                        <th style="width: 130px;">NISN</th>
                        <th>Nama Lengkap Peserta Didik</th>
                        <th style="width: 100px;">Kelas</th>
                        <th class="text-center font-monospace">WSM ($Q_i^{(1)}$)</th>
                        <th class="text-center font-monospace">WPM ($Q_i^{(2)}$)</th>
                        <th class="text-center font-monospace fw-bold">Skor Akhir ($Q_i$)</th>
                        <th class="text-center" style="width: 160px;">Ketetapan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($calculation['results'] as $res)
                        <tr>
                            <td class="text-center">
                                @if($res['peringkat'] == 1)
                                    <span class="badge bg-warning text-dark border fw-bold">1</span>
                                @elseif($res['peringkat'] == 2)
                                    <span class="badge bg-secondary text-white fw-bold">2</span>
                                @elseif($res['peringkat'] == 3)
                                    <span class="badge text-white fw-bold" style="background-color: #b45309;">3</span>
                                @else
                                    <span class="text-muted fw-semibold">{{ $res['peringkat'] }}</span>
                                @endif
                            </td>
                            <td><code>{{ $res['nisn'] }}</code></td>
                            <td class="fw-semibold text-dark">{{ $res['nama_lengkap'] }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $res['kelas'] }}</span></td>
                            <td class="text-center font-monospace text-muted">{{ number_format($res['wsm'], 4) }}</td>
                            <td class="text-center font-monospace text-muted">{{ number_format($res['wpm'], 4) }}</td>
                            <td class="text-center font-monospace fw-bold text-success">{{ number_format($res['qi'], 4) }}</td>
                            <td class="text-center">
                                @if($res['peringkat'] == 1)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Juara I (Utama)</span>
                                @elseif($res['peringkat'] == 2)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Juara II</span>
                                @elseif($res['peringkat'] == 3)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Juara III</span>
                                @else
                                    <span class="text-muted small">Nominasi</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tombol Simpan & Ajukan Pengesahan SK -->
    <div class="card p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-bold text-dark">Simpan Hasil Perankingan Periode Ini?</div>
                <div class="text-muted small">
                    Hasil perankingan akan disimpan ke tabel <code>hasil_waspas</code> dan diajukan ke modul Kepala Sekolah untuk pengesahan SK resmi.
                </div>
            </div>
            <form action="{{ route('admin.waspas.save') }}" method="POST">
                @csrf
                <input type="hidden" name="periode" value="{{ $selectedPeriode }}">
                <input type="hidden" name="lambda" value="{{ $lambda }}">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-save me-1"></i> Simpan & Teruskan ke Kepala Sekolah
                </button>
            </form>
        </div>
    </div>

    <!-- Rincian Matriks Perhitungan -->
    <div class="card mb-4">
        <div class="card-header">
            Rincian Matriks Perhitungan Matematis (Transparansi WASPAS)
        </div>
        <div class="card-body">
            <!-- 1. Matriks Keputusan X -->
            <div class="mb-4">
                <div class="fw-bold text-secondary mb-2 small text-uppercase">1. Matriks Keputusan Awal ($X$)</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-start">Nama Siswa</th>
                                @foreach($kriteriaList as $k)
                                    <th>{{ $k->kode_kriteria }} ({{ $k->jenis_atribut }})</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calculation['matrix_x'] as $item)
                                <tr>
                                    <td class="text-start fw-medium">{{ $item['siswa']->nama_lengkap }}</td>
                                    @foreach($kriteriaList as $k)
                                        <td class="font-monospace">{{ $item['values'][$k->id_kriteria] }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light fw-bold font-monospace">
                            <tr>
                                <td class="text-start text-success">Maksimum ($\max x_j$)</td>
                                @foreach($kriteriaList as $k)
                                    <td class="text-success">{{ $calculation['col_max'][$k->id_kriteria] }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="text-start text-danger">Minimum ($\min x_j$)</td>
                                @foreach($kriteriaList as $k)
                                    <td class="text-danger">{{ $calculation['col_min'][$k->id_kriteria] }}</td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- 2. Matriks Normalisasi R -->
            <div>
                <div class="fw-bold text-secondary mb-2 small text-uppercase">2. Matriks Ternormalisasi ($R$)</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-start">Nama Siswa</th>
                                @foreach($kriteriaList as $k)
                                    <th>{{ $k->kode_kriteria }} ($w = {{ number_format($k->bobot_bwm, 4) }}$)</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calculation['matrix_r'] as $item)
                                <tr>
                                    <td class="text-start fw-medium">{{ $item['siswa']->nama_lengkap }}</td>
                                    @foreach($kriteriaList as $k)
                                        <td class="font-monospace text-muted">{{ number_format($item['values'][$k->id_kriteria], 4) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
