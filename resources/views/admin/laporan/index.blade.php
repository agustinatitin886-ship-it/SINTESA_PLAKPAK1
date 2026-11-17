@extends('layouts.app')

@section('title', 'Cetak Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Laporan Perankingan</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Cetak & Ekspor Laporan Siswa Berprestasi</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.laporan.excel', ['periode' => $selectedPeriode, 'kelas' => $selectedKelas]) }}" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Ekspor CSV / Excel
        </a>
        <a href="{{ route('admin.laporan.cetak', ['periode' => $selectedPeriode, 'kelas' => $selectedKelas]) }}" target="_blank" class="btn btn-primary">
            <i class="bi bi-printer me-1"></i> Cetak Dokumen (PDF)
        </a>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="row g-3 align-items-center">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-secondary mb-1">Periode Akademik</label>
                <select name="periode" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($daftarPeriode as $p)
                        <option value="{{ $p }}" {{ $selectedPeriode == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-semibold text-secondary mb-1">Pilih Tingkat Kelas</label>
                <select name="kelas" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">-- Semua Kelas --</option>
                    @foreach($daftarKelas as $k)
                        <option value="{{ $k }}" {{ $selectedKelas == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-semibold text-secondary mb-1">Status Validasi</label>
                <div class="p-1 px-2 border rounded bg-light small">
                    @if($approval && $approval->status === 'disetujui')
                        <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Disahkan (SK: {{ $approval->nomor_sk ?? '-' }})</span>
                    @else
                        <span class="text-secondary"><i class="bi bi-clock"></i> Belum Disahkan Kepsek</span>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Rekapitulasi Penetapan Peringkat Siswa Berprestasi</span>
        <span class="small text-muted">Periode {{ $selectedPeriode }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-center" style="width: 70px;">Peringkat</th>
                    <th style="width: 130px;">NISN</th>
                    <th>Nama Lengkap</th>
                    <th style="width: 100px;">Kelas</th>
                    <th class="text-center font-monospace">WSM ($Q_i^{(1)}$)</th>
                    <th class="text-center font-monospace">WPM ($Q_i^{(2)}$)</th>
                    <th class="text-center font-monospace fw-bold">Skor Akhir ($Q_i$)</th>
                    <th class="text-center" style="width: 160px;">Ketetapan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan as $item)
                    <tr>
                        <td class="text-center">
                            @if($item->peringkat == 1)
                                <span class="badge bg-warning text-dark border fw-bold">1</span>
                            @elseif($item->peringkat == 2)
                                <span class="badge bg-secondary text-white fw-bold">2</span>
                            @elseif($item->peringkat == 3)
                                <span class="badge text-white fw-bold" style="background-color: #b45309;">3</span>
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
                            @if($item->peringkat == 1)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Juara I (Utama)</span>
                            @elseif($item->peringkat == 2)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Juara II</span>
                            @elseif($item->peringkat == 3)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Juara III</span>
                            @else
                                <span class="text-muted small">Nominasi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada data perankingan untuk filter yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
