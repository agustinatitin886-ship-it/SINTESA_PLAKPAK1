@extends('layouts.app')

@section('title', 'Penilaian Siswa')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1 small">
            <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('walikelas.penilaian.index') }}" class="text-decoration-none">Penilaian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Input Nilai Siswa</li>
        </ol>
    </nav>
    <h4 class="fw-bold text-dark mb-1">Penilaian Siswa: {{ $siswa->nama_lengkap }}</h4>
    <div class="text-muted small">NISN: <code>{{ $siswa->nisn }}</code> &bull; Kelas: <strong>{{ $siswa->kelas }}</strong></div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Formulir Pengisian Nilai Tiap Kriteria</div>
            <div class="card-body">
                <form action="{{ route('walikelas.penilaian.update', $siswa->id_siswa) }}" method="POST">
                    @csrf

                    @foreach($kriteria as $k)
                        @php
                            $val = old('nilai.' . $k->id_kriteria, $penilaianMap[$k->id_kriteria] ?? '');
                        @endphp
                        <div class="mb-3 p-3 border rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="font-monospace fw-bold me-1">{{ $k->kode_kriteria }}</span>
                                    <span class="fw-semibold text-dark">{{ $k->nama_kriteria }}</span>
                                </div>
                                <span class="badge {{ $k->jenis_atribut === 'Benefit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border" style="font-size: 11px;">
                                    Atribut: {{ $k->jenis_atribut }}
                                </span>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <div class="input-group input-group-sm">
                                        <input type="number" 
                                               step="any" 
                                               min="0" 
                                               name="nilai[{{ $k->id_kriteria }}]" 
                                               value="{{ $val }}" 
                                               class="form-control font-monospace fw-bold" 
                                               placeholder="Contoh: 85" 
                                               required>
                                        <span class="input-group-text">Poin</span>
                                    </div>
                                </div>
                                <div class="col-md-7 mt-2 mt-md-0">
                                    <small class="text-muted" style="font-size: 11px;">
                                        @if($k->jenis_atribut === 'Benefit')
                                            Semakin tinggi nilai, semakin menguntungkan peluang prestasi.
                                        @else
                                            Semakin rendah nilai/angka pelanggaran, semakin baik.
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Penilaian Siswa
                        </button>
                        <a href="{{ route('walikelas.penilaian.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
