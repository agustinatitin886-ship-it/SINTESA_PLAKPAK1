@extends('layouts.app')

@section('title', 'Verifikasi & Pengesahan SK')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('kepsek.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Verifikasi Hasil</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Verifikasi & Pengesahan Siswa Berprestasi</h4>
    </div>
    @if($approval && $approval->status === 'disetujui')
        <a href="{{ route('kepsek.verifikasi.sk', ['periode' => $selectedPeriode]) }}" target="_blank" class="btn btn-outline-success">
            <i class="bi bi-printer me-1"></i> Cetak SK Resmi
        </a>
    @endif
</div>

<!-- Form Validasi Kepala Sekolah -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Formulir Pengesahan Digital Kepala Sekolah</span>
        @if($approval && $approval->status === 'disetujui')
            <span class="badge bg-success">Status: Disetujui & Sah</span>
        @elseif($approval && $approval->status === 'ditolak')
            <span class="badge bg-danger">Status: Ditolak / Revisi</span>
        @else
            <span class="badge bg-warning text-dark">Status: Menunggu Persetujuan</span>
        @endif
    </div>
    <div class="card-body">
        <form action="{{ route('kepsek.verifikasi.approve') }}" method="POST">
            @csrf
            <input type="hidden" name="periode" value="{{ $selectedPeriode }}">

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-secondary">Nomor Surat Keputusan (SK)</label>
                    <input type="text" name="nomor_sk" class="form-control form-control-sm font-monospace" value="{{ old('nomor_sk', $approval?->nomor_sk ?? '421.2/084/SDN.PLK.1/' . date('Y')) }}" placeholder="Contoh: 421.2/084/SDN.PLK.1/2025" required>
                    <small class="text-muted">Nomor surat penetapan resmi sekolah.</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-secondary">Keputusan Validasi</label>
                    <select name="status" class="form-select form-select-sm fw-semibold" required>
                        <option value="disetujui" {{ old('status', $approval?->status) === 'disetujui' ? 'selected' : '' }}>
                            Setujui & Sahkan Hasil Perankingan
                        </option>
                        <option value="ditolak" {{ old('status', $approval?->status) === 'ditolak' ? 'selected' : '' }}>
                            Tolak / Minta Revisi Nilai
                        </option>
                        <option value="menunggu" {{ old('status', $approval?->status) === 'menunggu' ? 'selected' : '' }}>
                            Menunggu Keputusan
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-secondary">Pejabat Pengesah</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="{{ Auth::user()->nama_pengguna }}" readonly>
                    <small class="text-muted">Kepala SD Negeri Plakpak 1 Pegantenan</small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Catatan / Arahan Tambahan (Opsional)</label>
                <textarea name="catatan" rows="2" class="form-control form-control-sm" placeholder="Tuliskan arahan bila ada...">{{ old('catatan', $approval?->catatan) }}</textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    @if($approval && $approval->tanggal_persetujuan)
                        Terakhir divalidasi: <strong>{{ $approval->tanggal_persetujuan->format('d/m/Y, H:i') }} WIB</strong>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle me-1"></i> Perbarui Status Pengesahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('kepsek.verifikasi.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="periode" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($daftarPeriode as $p)
                        <option value="{{ $p }}" {{ $selectedPeriode == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <select name="kelas" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">-- Semua Kelas (Gabungan) --</option>
                    @foreach($daftarKelas as $k)
                        <option value="{{ $k }}" {{ $selectedKelas == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <a href="{{ route('kepsek.verifikasi.index') }}" class="btn btn-sm btn-light border w-100">Atur Ulang</a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Hasil Perankingan -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Peringkat Siswa Berprestasi Hasil WASPAS</span>
        <span class="small text-muted">{{ $hasil->count() }} Siswa</span>
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
                @forelse($hasil as $item)
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
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada hasil WASPAS untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
