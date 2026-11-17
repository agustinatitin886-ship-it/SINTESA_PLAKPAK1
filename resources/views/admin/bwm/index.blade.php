@extends('layouts.app')

@section('title', 'Kalkulator BWM')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kalkulator BWM</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Pembobotan Kriteria Metode Best-Worst (BWM)</h4>
    </div>
    @if($latestLog)
        <span class="badge bg-light text-dark border py-2 px-3">
            <i class="bi bi-clock-history me-1 text-primary"></i> Terakhir Dihitung: CR = <strong>{{ $latestLog->consistency_ratio }}</strong>
        </span>
    @endif
</div>

<!-- Informasi Metodologi BWM -->
<div class="card mb-4">
    <div class="card-body p-3">
        <div class="d-flex align-items-start gap-3">
            <div class="p-2 bg-light border rounded text-success">
                <i class="bi bi-info-circle-fill fs-5"></i>
            </div>
            <div>
                <div class="fw-bold text-dark mb-1">Metodologi Best-Worst Method (BWM)</div>
                <p class="text-muted small mb-0">
                    Metode BWM (Rezaei, 2015) melakukan perbandingan berpasangan antara kriteria terbaik (Best) terhadap semua kriteria lainnya ($a_{Bj}$) 
                    serta semua kriteria lainnya terhadap kriteria terburuk ($a_{jW}$). Sistem akan memecahkan optimasi linear programming untuk menghasilkan 
                    bobot yang konsisten ($CR \le 0.10$).
                </p>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.bwm.calculate') }}" method="POST" id="bwmForm">
    @csrf

    <!-- Langkah 1: Kriteria Terbaik & Terburuk -->
    <div class="card mb-4">
        <div class="card-header">
            1. Penentuan Kriteria Terbaik ($C_B$) dan Kriteria Terburuk ($C_W$)
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">
                        Kriteria Paling Prioritas / Terbaik ($C_B$) <span class="text-danger">*</span>
                    </label>
                    <select name="best_kriteria_id" id="bestSelect" class="form-select @error('best_kriteria_id') is-invalid @enderror" required onchange="handleCriteriaSelection()">
                        <option value="">-- Pilih Kriteria Terbaik --</option>
                        @foreach($kriteria as $k)
                            <option value="{{ $k->id_kriteria }}" {{ (old('best_kriteria_id', $latestLog?->best_kriteria_id) == $k->id_kriteria) ? 'selected' : '' }}>
                                {{ $k->kode_kriteria }} - {{ $k->nama_kriteria }} ({{ $k->jenis_atribut }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Kriteria yang dinilai paling berpengaruh terhadap prestasi siswa.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">
                        Kriteria Paling Rendah / Terburuk ($C_W$) <span class="text-danger">*</span>
                    </label>
                    <select name="worst_kriteria_id" id="worstSelect" class="form-select @error('worst_kriteria_id') is-invalid @enderror" required onchange="handleCriteriaSelection()">
                        <option value="">-- Pilih Kriteria Terburuk --</option>
                        @foreach($kriteria as $k)
                            <option value="{{ $k->id_kriteria }}" {{ (old('worst_kriteria_id', $latestLog?->worst_kriteria_id) == $k->id_kriteria) ? 'selected' : '' }}>
                                {{ $k->kode_kriteria }} - {{ $k->nama_kriteria }} ({{ $k->jenis_atribut }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Kriteria dengan tingkat prioritas terendah dibanding yang lain.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Panduan Skala Preferensi -->
    <div class="card mb-4 bg-light">
        <div class="card-body p-3">
            <div class="small text-secondary">
                <strong class="text-dark">Panduan Skala Perbandingan Saaty (1 s.d. 9):</strong>
                <span class="ms-1">
                    1 = Sama penting &bull; 
                    3 = Cukup lebih penting &bull; 
                    5 = Jelas lebih penting &bull; 
                    7 = Sangat jelas lebih penting &bull; 
                    9 = Mutlak lebih penting (angka genap 2, 4, 6, 8 nilai kompromi).
                </span>
            </div>
        </div>
    </div>

    <!-- Langkah 2: Best-to-Others Vector -->
    <div class="card mb-4">
        <div class="card-header">
            2. Vektor Perbandingan Kriteria Terbaik terhadap Lainnya ($A_B = a_{Bj}$)
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 120px;">Kode</th>
                        <th>Nama Kriteria</th>
                        <th style="width: 160px;" class="text-center">Tingkat Preferensi (1 - 9)</th>
                        <th>Penjelasan Linguistik</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kriteria as $k)
                        @php
                            $valBest = old('best_to_others.' . $k->id_kriteria, $latestLog?->vektor_best_to_others[$k->id_kriteria] ?? 1);
                        @endphp
                        <tr>
                            <td class="font-monospace fw-bold">{{ $k->kode_kriteria }}</td>
                            <td>{{ $k->nama_kriteria }}</td>
                            <td>
                                <select name="best_to_others[{{ $k->id_kriteria }}]" class="form-select text-center fw-bold best-input" id="best_input_{{ $k->id_kriteria }}" onchange="updateScaleText(this, 'best_desc_{{ $k->id_kriteria }}')">
                                    @for($s = 1; $s <= 9; $s++)
                                        <option value="{{ $s }}" {{ $valBest == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <span class="text-muted small" id="best_desc_{{ $k->id_kriteria }}">Sama penting (1)</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Langkah 3: Others-to-Worst Vector -->
    <div class="card mb-4">
        <div class="card-header">
            3. Vektor Perbandingan Kriteria Lainnya terhadap Kriteria Terburuk ($A_W = a_{jW}$)
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 120px;">Kode</th>
                        <th>Nama Kriteria</th>
                        <th style="width: 160px;" class="text-center">Tingkat Preferensi (1 - 9)</th>
                        <th>Penjelasan Linguistik</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kriteria as $k)
                        @php
                            $valWorst = old('others_to_worst.' . $k->id_kriteria, $latestLog?->vektor_others_to_worst[$k->id_kriteria] ?? 1);
                        @endphp
                        <tr>
                            <td class="font-monospace fw-bold">{{ $k->kode_kriteria }}</td>
                            <td>{{ $k->nama_kriteria }}</td>
                            <td>
                                <select name="others_to_worst[{{ $k->id_kriteria }}]" class="form-select text-center fw-bold worst-input" id="worst_input_{{ $k->id_kriteria }}" onchange="updateScaleText(this, 'worst_desc_{{ $k->id_kriteria }}')">
                                    @for($s = 1; $s <= 9; $s++)
                                        <option value="{{ $s }}" {{ $valWorst == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <span class="text-muted small" id="worst_desc_{{ $k->id_kriteria }}">Sama penting (1)</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tombol Eksekusi -->
    <div class="card p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                <i class="bi bi-shield-check me-1 text-success"></i> Sistem akan menjalankan metode optimasi Linear Programming Simpleks Dua Tahap
            </span>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-cpu me-1"></i> Hitung Bobot BWM & Verifikasi Konsistensi
            </button>
        </div>
    </div>
</form>

<!-- Riwayat Penghitungan Terakhir -->
@if($allLogs->isNotEmpty())
    <div class="card">
        <div class="card-header">
            Riwayat Pembobotan BWM Sebelumnya
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Waktu Eksekusi</th>
                        <th>Kriteria Terbaik</th>
                        <th>Kriteria Terburuk</th>
                        <th class="text-center">Nilai Optimal ($\xi^*$)</th>
                        <th class="text-center">Rasio Konsistensi (CR)</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allLogs as $idx => $lg)
                        <tr>
                            <td class="text-muted">{{ $idx + 1 }}</td>
                            <td>{{ $lg->created_at->format('d/m/Y, H:i') }} WIB</td>
                            <td><span class="badge bg-light text-dark border">{{ $lg->bestKriteria->kode_kriteria ?? '-' }} - {{ $lg->bestKriteria->nama_kriteria ?? '-' }}</span></td>
                            <td><span class="badge bg-light text-dark border">{{ $lg->worstKriteria->kode_kriteria ?? '-' }} - {{ $lg->worstKriteria->nama_kriteria ?? '-' }}</span></td>
                            <td class="text-center font-monospace">{{ $lg->xi_value ?? '-' }}</td>
                            <td class="text-center font-monospace fw-bold">{{ $lg->consistency_ratio }}</td>
                            <td class="text-center">
                                @if($lg->consistency_ratio <= 0.10)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Sangat Konsisten</span>
                                @else
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle">Cukup Konsisten</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
const scaleDesc = {
    1: 'Sama penting (Nilai 1)',
    2: 'Antara sama penting dan sedikit lebih penting (Nilai 2)',
    3: 'Sedikit lebih penting (Nilai 3)',
    4: 'Antara sedikit dan cukup penting (Nilai 4)',
    5: 'Cukup lebih penting (Nilai 5)',
    6: 'Antara cukup dan sangat jelas penting (Nilai 6)',
    7: 'Sangat jelas lebih penting (Nilai 7)',
    8: 'Antara sangat jelas dan mutlak penting (Nilai 8)',
    9: 'Mutlak jauh lebih penting (Nilai 9)'
};

function updateScaleText(elem, targetId) {
    const val = elem.value;
    const target = document.getElementById(targetId);
    if (target) {
        target.textContent = scaleDesc[val] || '';
    }
}

function handleCriteriaSelection() {
    const bestId = document.getElementById('bestSelect').value;
    const worstId = document.getElementById('worstSelect').value;

    if (bestId) {
        const bestElem = document.getElementById('best_input_' + bestId);
        if (bestElem) {
            bestElem.value = 1;
            bestElem.setAttribute('disabled', 'disabled');
            updateScaleText(bestElem, 'best_desc_' + bestId);
        }
    }

    if (worstId) {
        const worstElem = document.getElementById('worst_input_' + worstId);
        if (worstElem) {
            worstElem.value = 1;
            worstElem.setAttribute('disabled', 'disabled');
            updateScaleText(worstElem, 'worst_desc_' + worstId);
        }
    }

    document.querySelectorAll('.best-input').forEach(elem => {
        const id = elem.id.replace('best_input_', '');
        if (id !== bestId) elem.removeAttribute('disabled');
        updateScaleText(elem, 'best_desc_' + id);
    });

    document.querySelectorAll('.worst-input').forEach(elem => {
        const id = elem.id.replace('worst_input_', '');
        if (id !== worstId) elem.removeAttribute('disabled');
        updateScaleText(elem, 'worst_desc_' + id);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    handleCriteriaSelection();
});
</script>
@endpush
