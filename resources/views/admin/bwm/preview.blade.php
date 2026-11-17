@extends('layouts.app')

@section('title', 'Hasil Kalkulasi BWM')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1 small">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.bwm.index') }}" class="text-decoration-none">Kalkulator BWM</a></li>
            <li class="breadcrumb-item active" aria-current="page">Hasil Pembobotan</li>
        </ol>
    </nav>
    <h4 class="fw-bold text-dark mb-0">Hasil Kalkulasi Bobot & Evaluasi Konsistensi (BWM)</h4>
</div>

<!-- Status Evaluasi Konsistensi -->
<div class="card mb-4 {{ $isConsistent ? 'border-success' : 'border-warning' }}">
    <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 border rounded {{ $isConsistent ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}" style="font-size: 24px;">
                    <i class="bi {{ $isConsistent ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 {{ $isConsistent ? 'text-success' : 'text-dark' }}">
                        {{ $isConsistent ? 'Rasio Konsistensi Memenuhi Syarat (CR <= 0.10)' : 'Rasio Konsistensi Agak Tinggi (CR > 0.10)' }}
                    </h6>
                    <div class="text-muted small">
                        Nilai optimal $\xi^* = {{ $xi }}$ | Indeks Konsistensi $CI = {{ $ci }}$ | Diperoleh <strong>Rasio Konsistensi (CR) = {{ $cr }}</strong>.
                        {{ $isConsistent ? 'Bobot kriteria dinyatakan konsisten dan valid untuk digunakan pada perankingan WASPAS.' : 'Disarankan mengevaluasi kuesioner perbandingan bila diperlukan.' }}
                    </div>
                </div>
            </div>
            <div class="text-end d-none d-md-block">
                <span class="badge {{ $isConsistent ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2 fs-6">
                    CR = {{ round($cr, 4) }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Tabel Bobot -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Vektor Bobot Kriteria Hasil Optimasi ($w_j$)</span>
                <span class="small text-muted">Terbaik: {{ $bestKriteria->kode_kriteria }} | Terburuk: {{ $worstKriteria->kode_kriteria }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Kode</th>
                            <th>Nama Kriteria</th>
                            <th>Jenis Atribut</th>
                            <th class="text-center">Bobot ($w_j$)</th>
                            <th class="text-center" style="width: 120px;">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalW = 0; @endphp
                        @foreach($kriteria as $k)
                            @php
                                $w = $weights[$k->id_kriteria] ?? 0;
                                $totalW += $w;
                            @endphp
                            <tr>
                                <td class="font-monospace fw-bold">
                                    {{ $k->kode_kriteria }}
                                    @if($k->id_kriteria == $bestId)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 9px;">Terbaik</span>
                                    @elseif($k->id_kriteria == $worstId)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 9px;">Terburuk</span>
                                    @endif
                                </td>
                                <td class="fw-medium text-dark">{{ $k->nama_kriteria }}</td>
                                <td>
                                    <span class="badge {{ $k->jenis_atribut === 'Benefit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border">
                                        {{ $k->jenis_atribut }}
                                    </span>
                                </td>
                                <td class="text-center font-monospace fw-bold">{{ number_format($w, 4) }}</td>
                                <td class="text-center font-monospace">{{ round($w * 100, 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold bg-light">
                            <td colspan="3" class="text-end">Total Akumulasi:</td>
                            <td class="text-center font-monospace">{{ number_format($totalW, 4) }}</td>
                            <td class="text-center font-monospace text-success">{{ round($totalW * 100, 1) }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Grafik Visualisasi Bobot -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                Diagram Komposisi Bobot Kriteria
            </div>
            <div class="card-body">
                <div style="height: 240px; position: relative;">
                    <canvas id="chartWeights"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Konfirmasi & Simpan -->
<div class="card p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div class="fw-bold text-dark">Terapkan Bobot Hasil Perhitungan?</div>
            <div class="text-muted small">Bobot akan diperbarui ke basis data kriteria dan dapat langsung digunakan oleh kalkulator WASPAS.</div>
        </div>
        <form action="{{ route('admin.bwm.apply') }}" method="POST">
            @csrf
            <input type="hidden" name="best_kriteria_id" value="{{ $bestId }}">
            <input type="hidden" name="worst_kriteria_id" value="{{ $worstId }}">
            @foreach($weights as $idKriteria => $val)
                <input type="hidden" name="weights[{{ $idKriteria }}]" value="{{ $val }}">
            @endforeach
            <input type="hidden" name="best_to_others" value="{{ json_encode($bestToOthers) }}">
            <input type="hidden" name="others_to_worst" value="{{ json_encode($othersToWorst) }}">
            <input type="hidden" name="consistency_ratio" value="{{ $cr }}">
            <input type="hidden" name="xi_value" value="{{ $xi }}">
            <input type="hidden" name="periode" value="{{ $periode }}">

            <div class="d-flex gap-2">
                <a href="{{ route('admin.bwm.index') }}" class="btn btn-outline-secondary">Kembali</a>
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-check2-circle me-1"></i> Terapkan Bobot ke Sistem
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('chartWeights').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($kriteria->pluck('kode_kriteria')) !!},
            datasets: [{
                label: 'Bobot (%)',
                data: {!! json_encode(array_values(array_map(fn($w) => round($w * 100, 2), $weights))) !!},
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
</script>
@endpush
