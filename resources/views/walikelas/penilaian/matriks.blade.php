@extends('layouts.app')

@section('title', 'Matriks Nilai Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('walikelas.penilaian.index') }}" class="text-decoration-none">Penilaian</a></li>
                <li class="breadcrumb-item active" aria-current="page">Formulir Matriks</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Formulir Matriks Penilaian Siswa: {{ $kelas }}</h4>
    </div>
    <a href="{{ route('walikelas.penilaian.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<form action="{{ route('walikelas.penilaian.storeMatriks') }}" method="POST">
    @csrf

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Matriks Isian Nilai Mentah Seluruh Siswa {{ $kelas }}</span>
            <span class="small text-muted">Isi angka langsung pada kolom masing-masing kriteria</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 text-center">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="min-width: 190px;" class="text-start">Nama Siswa</th>
                        @foreach($kriteria as $k)
                            <th style="min-width: 130px;">
                                <div class="font-monospace fw-bold">{{ $k->kode_kriteria }}</div>
                                <div class="small fw-normal text-muted" style="font-size: 11px;">{{ $k->nama_kriteria }}</div>
                                <span class="badge {{ $k->jenis_atribut === 'Benefit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border" style="font-size: 10px;">
                                    {{ $k->jenis_atribut }}
                                </span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaList as $idx => $s)
                        <tr>
                            <td class="text-muted">{{ $idx + 1 }}</td>
                            <td class="text-start">
                                <div class="fw-semibold text-dark">{{ $s->nama_lengkap }}</div>
                                <code style="font-size: 11px;">{{ $s->nisn }}</code>
                            </td>
                            @foreach($kriteria as $k)
                                @php
                                    $currentVal = $s->getNilaiKriteria($k->id_kriteria);
                                @endphp
                                <td class="p-1">
                                    <input type="number" 
                                           step="any" 
                                           min="0" 
                                           name="nilai[{{ $s->id_siswa }}][{{ $k->id_kriteria }}]" 
                                           value="{{ old('nilai.' . $s->id_siswa . '.' . $k->id_kriteria, $currentVal) }}" 
                                           class="form-control form-control-sm text-center font-monospace" 
                                           placeholder="Nilai...">
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + $kriteria->count() }}" class="py-4 text-muted">Tidak ada siswa pada kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light p-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">
                <i class="bi bi-info-circle me-1"></i> Nilai yang diinput akan disimpan sebagai data penilaian mentah.
            </span>
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-save me-1"></i> Simpan Seluruh Nilai Matriks Kelas
            </button>
        </div>
    </div>
</form>
@endsection
