@extends('layouts.app')

@section('title', 'Tambah Kriteria')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.kriteria.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Kriteria
    </a>
    <h3 class="fw-bold text-dark mb-1">Tambah Kriteria Baru</h3>
    <p class="text-muted mb-0">Definisikan tolak ukur penilaian baru di SINTESA SDN PLAKPAK 1</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Formulir Tambah Kriteria</div>
            <div class="card-body">
                <form action="{{ route('admin.kriteria.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Kriteria <span class="text-danger">*</span></label>
                        <input type="text" name="kode_kriteria" class="form-control @error('kode_kriteria') is-invalid @enderror" value="{{ old('kode_kriteria', $suggestedCode) }}" placeholder="Misal: C1, C2" required>
                        <small class="text-muted">Kode unik kriteria untuk perhitungan matematis.</small>
                        @error('kode_kriteria')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kriteria <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kriteria" class="form-control @error('nama_kriteria') is-invalid @enderror" value="{{ old('nama_kriteria') }}" placeholder="Misal: Nilai Rapor / Kehadiran" required>
                        @error('nama_kriteria')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Jenis Atribut <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="card p-3 border cursor-pointer h-100">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_atribut" value="Benefit" {{ old('jenis_atribut', 'Benefit') === 'Benefit' ? 'checked' : '' }} required>
                                        <span class="fw-bold text-success d-block">Benefit (Keuntungan)</span>
                                        <small class="text-muted">Semakin tinggi nilai siswa, semakin menguntungkan.</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="card p-3 border cursor-pointer h-100">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_atribut" value="Cost" {{ old('jenis_atribut') === 'Cost' ? 'checked' : '' }}>
                                        <span class="fw-bold text-danger d-block">Cost (Biaya / Kerugian)</span>
                                        <small class="text-muted">Semakin rendah nilai siswa, semakin baik (contoh: pelanggaran).</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @error('jenis_atribut')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Kriteria
                        </button>
                        <a href="{{ route('admin.kriteria.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

