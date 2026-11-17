@extends('layouts.app')

@section('title', 'Rekap Juara per Kelas')

@section('content')
<!-- Header & Breadcrumb -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.laporan.index') }}" class="text-decoration-none">Laporan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rekap per Kelas</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Rekapitulasi Siswa Berprestasi per Jenjang Kelas</h4>
        <small class="text-muted">Hasil evaluasi objektif BWM & WASPAS otomatis tanpa rekap manual</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.laporan.index', ['periode' => $selectedPeriode]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-list-ol me-1"></i> Rekap Gabungan Sekolah
        </a>
        <a href="{{ route('admin.laporan.cetak_per_kelas', ['periode' => $selectedPeriode]) }}" target="_blank" class="btn btn-primary">
            <i class="bi bi-printer me-1"></i> Cetak Rekap per Kelas (PDF)
        </a>
    </div>
</div>

<!-- Filter Periode / Tahun Ajaran -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.laporan.per_kelas') }}" class="row g-3 align-items-center">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-secondary mb-1">Tahun Ajaran / Periode Akademik</label>
                <select name="periode" class="form-select form-select-sm fw-semibold" onchange="this.form.submit()">
                    @foreach($daftarPeriode as $p)
                        <option value="{{ $p }}" {{ $selectedPeriode == $p ? 'selected' : '' }}>Tahun Ajaran {{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-semibold text-secondary mb-1">Status Pengesahan Kepala Sekolah</label>
                <div class="p-1 px-3 border rounded bg-light small">
                    @if($approval && $approval->status === 'disetujui')
                        <span class="text-success fw-bold">
                            <i class="bi bi-patch-check-fill me-1"></i> Sah & Disetujui (No. SK: {{ $approval->nomor_sk ?? '-' }})
                        </span>
                    @elseif($approval && $approval->status === 'ditolak')
                        <span class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-1"></i> Perlu Revisi</span>
                    @else
                        <span class="text-warning fw-semibold text-dark"><i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi SK</span>
                    @endif
                </div>
            </div>
            <div class="col-md-3 text-md-end mt-3 mt-md-4">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                    <i class="bi bi-cpu me-1"></i> Rekapitulasi Otomatis Aktif
                </span>
            </div>
        </form>
    </div>
</div>

@if($juaraUmum)
    <!-- Podium Juara Umum Sekolah -->
    <div class="card mb-4 border-start border-4 border-start-warning" style="background: linear-gradient(to right, #fffbeb, #ffffff);">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-warning text-dark border border-warning-subtle fw-bold px-2 py-1">
                            <i class="bi bi-trophy-fill me-1 text-warning-emphasis"></i> JUARA UMUM SEKOLAH TAHUN AJARAN {{ $selectedPeriode }}
                        </span>
                        <span class="badge bg-light text-dark border">{{ $juaraUmum->siswa->kelas ?? '-' }}</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $juaraUmum->siswa->nama_lengkap ?? '-' }}</h5>
                    <div class="text-muted small">
                        NISN: <code>{{ $juaraUmum->siswa->nisn ?? '-' }}</code> &bull; 
                        Skor WASPAS ($Q_i$): <strong class="text-success font-monospace">{{ number_format($juaraUmum->nilai_qi, 4) }}</strong> &bull; 
                        Status: <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $juaraUmum->status_kelayakan }}</span>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="d-inline-block text-center p-2 px-3 border rounded bg-white shadow-sm">
                        <div class="text-muted small" style="font-size: 11px;">Skor Tertinggi Sekolah</div>
                        <div class="fs-4 fw-bold font-monospace text-success">{{ number_format($juaraUmum->nilai_qi, 4) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Ringkasan Juara 1 Tiap Jenjang Kelas -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <i class="bi bi-award me-1 text-warning"></i> Daftar Siswa Peringkat 1 di Setiap Jenjang Kelas (Tahun Ajaran {{ $selectedPeriode }})
    </div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($juaraSatuPerKelas as $kls => $j)
                <div class="col-sm-6 col-lg-4">
                    <div class="p-3 border rounded h-100 bg-light position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary text-white fw-bold">{{ $kls }}</span>
                            <span class="badge bg-warning text-dark border border-warning-subtle fw-bold">Juara 1 Kelas</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $j->siswa->nama_lengkap ?? '-' }}</h6>
                        <div class="text-muted small mb-2">
                            NISN: <code>{{ $j->siswa->nisn ?? '-' }}</code>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted small">Skor WASPAS:</span>
                            <span class="font-monospace fw-bold text-success">{{ number_format($j->nilai_qi, 4) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-3 text-center text-muted">
                    Belum ada data perankingan untuk tahun ajaran {{ $selectedPeriode }}.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Tab Navigasi & Rincian Juara Tiap Kelas -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Rincian Kelayakan & Peringkat Seluruh Siswa per Kelas</span>
        <span class="small text-muted">Tersusun otomatis berdasarkan nilai BWM-WASPAS</span>
    </div>
    <div class="card-body p-0">
        <!-- Tabs Navigasi Kelas -->
        <ul class="nav nav-tabs px-3 pt-3 bg-light border-bottom" id="classTabs" role="tablist">
            @foreach($rekapPerKelas as $kls => $siswaList)
                @php $slug = \Illuminate\Support\Str::slug($kls); @endphp
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }} fw-semibold" 
                            id="tab-{{ $slug }}" 
                            data-bs-toggle="tab" 
                            data-bs-target="#content-{{ $slug }}" 
                            type="button" 
                            role="tab">
                        {{ $kls }} <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $siswaList->count() }}</span>
                    </button>
                </li>
            @endforeach
        </ul>

        <!-- Konten Tab Tiap Kelas -->
        <div class="tab-content p-3" id="classTabsContent">
            @forelse($rekapPerKelas as $kls => $siswaList)
                @php $slug = \Illuminate\Support\Str::slug($kls); @endphp
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="content-{{ $slug }}" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Daftar Peringkat Siswa {{ $kls }}</h6>
                            <small class="text-muted">Tahun Ajaran {{ $selectedPeriode }} &bull; Total: {{ $siswaList->count() }} Siswa</small>
                        </div>
                        <a href="{{ route('admin.laporan.cetak_per_kelas', ['periode' => $selectedPeriode, 'kelas' => $kls]) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-printer me-1"></i> Cetak Rekap {{ $kls }}
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 100px;" class="text-center">Peringkat Kelas</th>
                                    <th style="width: 130px;">NISN</th>
                                    <th>Nama Lengkap Peserta Didik</th>
                                    <th style="width: 70px;" class="text-center">L/P</th>
                                    <th class="text-center font-monospace">WSM ($Q_i^{(1)}$)</th>
                                    <th class="text-center font-monospace">WPM ($Q_i^{(2)}$)</th>
                                    <th class="text-center font-monospace fw-bold">Skor Akhir ($Q_i$)</th>
                                    <th class="text-center" style="width: 100px;">Peringkat Sekolah</th>
                                    <th style="width: 220px;">Status Kelayakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswaList as $item)
                                    <tr>
                                        <td class="text-center">
                                            @if($item->peringkat_kelas == 1)
                                                <span class="badge bg-warning text-dark border border-warning-subtle fw-bold px-2 py-1">
                                                    <i class="bi bi-trophy-fill me-1"></i> Juara 1
                                                </span>
                                            @elseif($item->peringkat_kelas == 2)
                                                <span class="badge bg-secondary text-white fw-bold px-2 py-1">Juara 2</span>
                                            @elseif($item->peringkat_kelas == 3)
                                                <span class="badge text-white fw-bold px-2 py-1" style="background-color: #b45309;">Juara 3</span>
                                            @else
                                                <span class="text-muted fw-semibold">{{ $item->peringkat_kelas }}</span>
                                            @endif
                                        </td>
                                        <td><code>{{ $item->siswa->nisn ?? '-' }}</code></td>
                                        <td class="fw-semibold text-dark">{{ $item->siswa->nama_lengkap ?? '-' }}</td>
                                        <td class="text-center">
                                            @if(($item->siswa->jenis_kelamin ?? 'L') === 'L')
                                                <span class="badge bg-light text-primary border">L</span>
                                            @else
                                                <span class="badge bg-light text-danger border">P</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-monospace text-muted">{{ number_format($item->nilai_wsm, 4) }}</td>
                                        <td class="text-center font-monospace text-muted">{{ number_format($item->nilai_wpm, 4) }}</td>
                                        <td class="text-center font-monospace fw-bold text-success">{{ number_format($item->nilai_qi, 4) }}</td>
                                        <td class="text-center font-monospace text-secondary">
                                            #{{ $item->peringkat }}
                                        </td>
                                        <td>
                                            @if($item->peringkat_kelas == 1)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    Sangat Layak — Juara 1
                                                </span>
                                            @elseif($item->peringkat_kelas == 2)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    Layak — Juara 2
                                                </span>
                                            @elseif($item->peringkat_kelas == 3)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    Layak — Juara 3
                                                </span>
                                            @else
                                                <span class="text-muted small">Kandidat Berprestasi</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="py-4 text-center text-muted">
                    Belum ada data perankingan untuk tahun ajaran {{ $selectedPeriode }}.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

