@extends('layouts.app')

@section('title', 'Input Nilai Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penilaian Siswa</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Input Penilaian Siswa: {{ $kelas }}</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('walikelas.penilaian.rekap') }}" class="btn btn-outline-secondary">
            <i class="bi bi-card-checklist me-1"></i> Rekapitulasi Nilai
        </a>
        <a href="{{ route('walikelas.penilaian.matriks') }}" class="btn btn-primary">
            <i class="bi bi-table me-1"></i> Formulir Matriks Cepat
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Siswa {{ $kelas }} & Status Kelengkapan Nilai</span>
        <span class="small text-muted">{{ $siswaList->count() }} Siswa</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th style="width: 140px;">NISN</th>
                    <th>Nama Lengkap Peserta Didik</th>
                    <th style="width: 70px;" class="text-center">L/P</th>
                    <th class="text-center" style="width: 140px;">Kriteria Terisi</th>
                    <th class="text-center" style="width: 120px;">Status</th>
                    <th class="text-center" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswaList as $idx => $s)
                    @php
                        $graded = $s->penilaian->count();
                        $complete = ($totalKriteria > 0 && $graded >= $totalKriteria);
                    @endphp
                    <tr>
                        <td class="text-center text-muted">{{ $idx + 1 }}</td>
                        <td><code>{{ $s->nisn }}</code></td>
                        <td class="fw-medium text-dark">{{ $s->nama_lengkap }}</td>
                        <td class="text-center">
                            @if($s->jenis_kelamin === 'L')
                                <span class="badge bg-light text-primary border">L</span>
                            @else
                                <span class="badge bg-light text-danger border">P</span>
                            @endif
                        </td>
                        <td class="text-center font-monospace">
                            <strong>{{ $graded }}</strong> / {{ $totalKriteria }}
                        </td>
                        <td class="text-center">
                            @if($complete)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Lengkap</span>
                            @else
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle">Belum Selesai</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('walikelas.penilaian.edit', $s->id_siswa) }}" class="btn btn-sm btn-outline-primary py-1 px-3">
                                <i class="bi bi-pencil me-1"></i>{{ $complete ? 'Ubah Nilai' : 'Masukkan Nilai' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada siswa pada {{ $kelas }}.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
