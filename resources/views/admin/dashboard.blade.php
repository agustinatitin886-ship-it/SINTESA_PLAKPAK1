@extends('layouts.app')

@section('title', 'Dasbor Administrator')

@section('content')
<!-- Header & Breadcrumb -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dasbor Administrator</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Panel Kendali Administrator</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.bwm.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-sliders me-1"></i> Pembobotan BWM
        </a>
        <a href="{{ route('admin.waspas.index') }}" class="btn btn-primary">
            <i class="bi bi-calculator me-1"></i> Perankingan WASPAS
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon bg-light text-success border">
                <i class="bi bi-list-check"></i>
            </div>
            <div>
                <div class="kpi-value">{{ $totalKriteria }}</div>
                <div class="kpi-label">Kriteria Penilaian</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon bg-light text-primary border">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <div class="kpi-value">{{ $totalSiswa }}</div>
                <div class="kpi-label">Total Siswa Kandidat</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon bg-light text-info border">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="kpi-value">{{ $siswaTernilai }} / {{ $totalSiswa }}</div>
                <div class="kpi-label">Nilai Lengkap Terisi</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon bg-light border {{ $approval && $approval->status === 'disetujui' ? 'text-success' : 'text-warning' }}">
                <i class="bi bi-patch-check"></i>
            </div>
            <div>
                <div class="kpi-value" style="font-size: 16px;">
                    @if($approval && $approval->status === 'disetujui')
                        <span class="badge bg-success">Disahkan</span>
                    @elseif($approval && $approval->status === 'ditolak')
                        <span class="badge bg-danger">Perlu Revisi</span>
                    @else
                        <span class="badge bg-warning text-dark">Menunggu SK</span>
                    @endif
                </div>
                <div class="kpi-label">Status SK Kepala Sekolah</div>
            </div>
        </div>
    </div>
</div>

<!-- Status Operasional Penilaian Card -->
<div class="card mb-4 border-start border-4 border-start-success">
    <div class="card-body p-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Status Terkini Sistem</span>
                    <span class="text-muted small">&bull; Periode Akademik: <strong>{{ $latestPeriode }}</strong></span>
                </div>
                <p class="text-secondary small mb-0">
                    @if($bwmLog)
                        Pembobotan kriteria metode BWM telah tersimpan dengan <strong>Rasio Konsistensi (CR) = {{ $bwmLog->consistency_ratio }}</strong>. 
                        Hasil WASPAS telah dikalkulasi untuk <strong>{{ $totalHasilWaspas }} siswa</strong>.
                    @else
                        Pembobotan BWM belum dijalankan. Buka menu Kalkulator BWM untuk menghitung bobot kriteria.
                    @endif
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-2 mt-lg-0">
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Rekapitulasi Laporan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <!-- Chart 1: Bobot Kriteria -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Distribusi Bobot Kriteria (BWM)</span>
                <a href="{{ route('admin.bwm.index') }}" class="small text-decoration-none">Kelola Bobot &rarr;</a>
            </div>
            <div class="card-body">
                <div style="height: 250px; position: relative;">
                    <canvas id="chartBobotKriteria"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 2: Top 5 Siswa -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Peringkat 5 Tertinggi Skor Akhir WASPAS (Qi)</span>
                <a href="{{ route('admin.waspas.index') }}" class="small text-decoration-none">Rincian Perankingan &rarr;</a>
            </div>
            <div class="card-body">
                <div style="height: 250px; position: relative;">
                    <canvas id="chartTopSiswa"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Nominasi Teratas -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Siswa Peringkat Teratas (Periode {{ $latestPeriode }})</span>
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua Data</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;" class="text-center">Peringkat</th>
                    <th style="width: 130px;">NISN</th>
                    <th>Nama Lengkap</th>
                    <th style="width: 100px;">Kelas</th>
                    <th class="text-center font-monospace">WSM ($Q_i^{(1)}$)</th>
                    <th class="text-center font-monospace">WPM ($Q_i^{(2)}$)</th>
                    <th class="text-center font-monospace fw-bold">Skor Akhir ($Q_i$)</th>
                    <th class="text-center" style="width: 150px;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topSiswa as $item)
                    <tr>
                        <td class="text-center">
                            @if($item->peringkat == 1)
                                <span class="badge bg-warning text-dark border border-warning-subtle fw-bold">Juara 1</span>
                            @elseif($item->peringkat == 2)
                                <span class="badge bg-secondary text-white fw-bold">Juara 2</span>
                            @elseif($item->peringkat == 3)
                                <span class="badge text-white fw-bold" style="background-color: #b45309;">Juara 3</span>
                            @else
                                <span class="text-muted fw-semibold">{{ $item->peringkat }}</span>
                            @endif
                        </td>
                        <td><code>{{ $item->siswa->nisn ?? '-' }}</code></td>
                        <td class="fw-semibold text-dark">{{ $item->siswa->nama_lengkap ?? '-' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $item->siswa->kelas ?? '-' }}</span></td>
                        <td class="text-center font-monospace text-muted">{{ number_format($item->nilai_wsm, 4) }}</td>
                        <td class="text-center font-monospace text-muted">{{ number_format($item->nilai_wpm, 4) }}</td>
                        <td class="text-center font-monospace fw-bold text-success">{{ number_format($item->nilai_qi, 4) }}</td>
                        <td class="text-center">
                            @if($item->peringkat <= 3)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    Siswa Berprestasi
                                </span>
                            @else
                                <span class="text-muted small">Nominasi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Belum ada hasil kalkulasi perankingan. Silakan buka menu <strong>Kalkulator WASPAS</strong>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Chart Bobot Kriteria
    const ctxBobot = document.getElementById('chartBobotKriteria').getContext('2d');
    new Chart(ctxBobot, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartKriteriaLabels) !!},
            datasets: [{
                data: {!! json_encode($chartKriteriaWeights) !!},
                backgroundColor: [
                    '#195e38',
                    '#0284c7',
                    '#b45309',
                    '#475569',
                    '#dc2626',
                    '#0d9488'
                ],
                borderWidth: 1,
                borderColor: '#cbd5e1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: { size: 11, family: 'Plus Jakarta Sans' }
                    }
                }
            }
        }
    });

    // Chart Top Siswa
    const ctxTop = document.getElementById('chartTopSiswa').getContext('2d');
    new Chart(ctxTop, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartTopLabels) !!},
            datasets: [{
                label: 'Skor WASPAS (Qi)',
                data: {!! json_encode($chartTopScores) !!},
                backgroundColor: '#195e38',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: {
                    min: 0,
                    max: 1,
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                },
                y: {
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' } }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endpush
