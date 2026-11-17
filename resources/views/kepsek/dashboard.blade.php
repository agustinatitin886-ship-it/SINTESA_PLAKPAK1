@extends('layouts.app')

@section('title', 'Dasbor Pimpinan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('kepsek.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dasbor Pimpinan</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Dasbor Pimpinan (Kepala Sekolah)</h4>
    </div>
    <a href="{{ route('kepsek.verifikasi.index') }}" class="btn btn-primary">
        <i class="bi bi-patch-check me-1"></i> Verifikasi & Pengesahan SK
    </a>
</div>

<!-- Status Pengesahan Banner -->
<div class="card mb-4 border-start border-4 {{ $approval && $approval->status === 'disetujui' ? 'border-start-success' : 'border-start-warning' }}">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 border rounded {{ $approval && $approval->status === 'disetujui' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark' }}" style="font-size: 22px;">
                    <i class="bi {{ $approval && $approval->status === 'disetujui' ? 'bi-check2-all' : 'bi-hourglass-split' }}"></i>
                </div>
                <div>
                    <div class="small text-muted">Status Pengesahan Periode {{ $periode }}</div>
                    <div class="fw-bold text-dark fs-6">
                        @if($approval && $approval->status === 'disetujui')
                            Hasil Perankingan Siswa Berprestasi Telah Disahkan Resmi
                        @elseif($approval && $approval->status === 'ditolak')
                            Status: Ditolak / Memerlukan Revisi
                        @else
                            Menunggu Verifikasi & Persetujuan Anda
                        @endif
                    </div>
                    <div class="text-muted small">
                        @if($approval && $approval->status === 'disetujui')
                            Nomor SK: <strong>{{ $approval->nomor_sk ?? '-' }}</strong> &bull; Disahkan pada {{ $approval->tanggal_persetujuan?->format('d/m/Y, H:i') }} WIB
                        @else
                            Kalkulasi WASPAS telah selesai diproses oleh Staf Kurikulum/TU dan siap diverifikasi.
                        @endif
                    </div>
                </div>
            </div>
            <div>
                @if($approval && $approval->status === 'disetujui')
                    <a href="{{ route('kepsek.verifikasi.sk') }}" target="_blank" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-printer me-1"></i> Cetak Dokumen SK
                    </a>
                @else
                    <a href="{{ route('kepsek.verifikasi.index') }}" class="btn btn-sm btn-warning">
                        Verifikasi Hasil &rarr;
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Podium 3 Siswa Terbaik -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Tiga Peringkat Tertinggi Siswa Berprestasi (Periode {{ $periode }})</span>
        <span class="small text-muted">Skor Tertinggi: <strong>{{ number_format($highestScore, 4) }}</strong></span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($top3 as $item)
                <div class="col-md-4">
                    <div class="p-3 border rounded text-center h-100 bg-light">
                        <div class="mb-2">
                            @if($item->peringkat == 1)
                                <span class="badge bg-warning text-dark border px-3 py-1 fw-bold">JUARA 1 (UTAMA)</span>
                            @elseif($item->peringkat == 2)
                                <span class="badge bg-secondary text-white border px-3 py-1 fw-bold">JUARA 2</span>
                            @elseif($item->peringkat == 3)
                                <span class="badge text-white border px-3 py-1 fw-bold" style="background-color: #b45309;">JUARA 3</span>
                            @endif
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $item->siswa->nama_lengkap ?? '-' }}</h6>
                        <div class="text-muted small mb-2">NISN: <code>{{ $item->siswa->nisn ?? '-' }}</code> &bull; {{ $item->siswa->kelas ?? '-' }}</div>
                        <div class="fs-4 fw-bold font-monospace text-success">{{ number_format($item->nilai_qi, 4) }}</div>
                        <div class="text-muted small" style="font-size: 11px;">Skor Akhir WASPAS (Qi)</div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-3 text-center text-muted">Belum ada peringkat siswa yang tersedia.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Rekap Juara 1 Tiap Jenjang Kelas (Tanpa Rekap Manual) -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center bg-light">
        <span class="fw-bold text-dark"><i class="bi bi-award-fill text-warning me-1"></i> Siswa Layak Juara 1 di Setiap Jenjang Kelas (Tahun Ajaran {{ $periode }})</span>
        <a href="{{ route('admin.laporan.per_kelas', ['periode' => $periode]) }}" class="small text-decoration-none">
            Lihat Rekap Lengkap Semua Kelas &rarr;
        </a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($juaraPerKelas as $kls => $j)
                <div class="col-sm-6 col-lg-4">
                    <div class="p-3 border rounded h-100 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary text-white fw-bold">{{ $kls }}</span>
                            <span class="badge bg-warning text-dark border border-warning-subtle fw-bold">Juara 1</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $j->siswa->nama_lengkap ?? '-' }}</h6>
                        <div class="text-muted small mb-2">NISN: <code>{{ $j->siswa->nisn ?? '-' }}</code></div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted small">Skor WASPAS:</span>
                            <span class="font-monospace fw-bold text-success">{{ number_format($j->nilai_qi, 4) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-3 text-muted">Belum ada data perankingan per kelas.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                Rata-rata Skor WASPAS per Jenjang Kelas (%)
            </div>
            <div class="card-body">
                <div style="height: 250px; position: relative;">
                    <canvas id="chartPerformaKelas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                Rata-rata Ketercapaian Kriteria Sekolah
            </div>
            <div class="card-body">
                <div style="height: 250px; position: relative;">
                    <canvas id="chartKriteriaRadar"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctxKelas = document.getElementById('chartPerformaKelas').getContext('2d');
    new Chart(ctxKelas, {
        type: 'bar',
        data: {
            labels: {!! json_encode($kelasLabels) !!},
            datasets: [{
                label: 'Rata-rata Skor WASPAS',
                data: {!! json_encode($kelasAvgScores) !!},
                backgroundColor: '#195e38',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(v) { return v + '%'; },
                        font: { size: 11, family: 'Plus Jakarta Sans' }
                    }
                },
                x: {
                    ticks: { font: { size: 11, family: 'Plus Jakarta Sans', weight: '600' } }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    const ctxRadar = document.getElementById('chartKriteriaRadar').getContext('2d');
    new Chart(ctxRadar, {
        type: 'radar',
        data: {
            labels: {!! json_encode($kriteriaLabels) !!},
            datasets: [{
                label: 'Rata-rata Nilai',
                data: {!! json_encode($kriteriaAverages) !!},
                backgroundColor: 'rgba(25, 94, 56, 0.15)',
                borderColor: '#195e38',
                pointBackgroundColor: '#195e38',
                pointBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    suggestedMin: 0,
                    suggestedMax: 100,
                    ticks: { stepSize: 20 }
                }
            }
        }
    });
</script>
@endpush
