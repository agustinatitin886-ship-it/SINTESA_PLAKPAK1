@extends('layouts.app')

@section('title', 'Rekapitulasi Nilai')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('walikelas.penilaian.index') }}" class="text-decoration-none">Penilaian</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rekapitulasi Nilai</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Rekapitulasi Nilai Mentah Siswa: {{ $kelas }}</h4>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary">
        <i class="bi bi-printer me-1"></i> Cetak Rekap Kelas
    </button>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Tabel Rekap Nilai Mentah Siswa {{ $kelas }}</span>
        <span class="small text-muted">{{ $siswaList->count() }} Siswa</span>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle mb-0 text-center">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="min-width: 180px;" class="text-start">Nama Siswa</th>
                    <th style="width: 120px;">NISN</th>
                    @foreach($kriteria as $k)
                        <th>
                            <div class="font-monospace fw-bold">{{ $k->kode_kriteria }}</div>
                            <div class="small fw-normal text-muted" style="font-size: 11px;">{{ $k->nama_kriteria }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($siswaList as $idx => $s)
                    <tr>
                        <td class="text-muted">{{ $idx + 1 }}</td>
                        <td class="text-start fw-medium text-dark">{{ $s->nama_lengkap }}</td>
                        <td><code>{{ $s->nisn }}</code></td>
                        @foreach($kriteria as $k)
                            @php
                                $val = $s->getNilaiKriteria($k->id_kriteria);
                            @endphp
                            <td class="font-monospace {{ $val === null ? 'text-danger' : 'text-dark' }}">
                                {{ $val !== null ? number_format($val, 1) : '-' }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 3 + $kriteria->count() }}" class="py-4 text-muted">Belum ada data siswa di kelas ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
