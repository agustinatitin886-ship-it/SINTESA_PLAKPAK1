@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Siswa
    </a>
    <h3 class="fw-bold text-dark mb-1">Tambah Siswa Baru (Alternatif)</h3>
    <p class="text-muted mb-0">Daftarkan kandidat nominasi siswa berprestasi</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Formulir Data Siswa</div>
            <div class="card-body">
                <form action="{{ route('admin.siswa.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Induk Siswa Nasional (NISN) <span class="text-danger">*</span></label>
                        <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn') }}" placeholder="10 digit nomor NISN" required>
                        @error('nisn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" placeholder="Nama sesuai rapor / akta" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas" class="form-select @error('kelas') is-invalid @enderror" required>
                                <option value="">-- Pilih Kelas --</option>
                                <option value="Kelas 1" {{ old('kelas') == 'Kelas 1' ? 'selected' : '' }}>Kelas 1</option>
                                <option value="Kelas 2" {{ old('kelas') == 'Kelas 2' ? 'selected' : '' }}>Kelas 2</option>
                                <option value="Kelas 3" {{ old('kelas') == 'Kelas 3' ? 'selected' : '' }}>Kelas 3</option>
                                <option value="Kelas 4" {{ old('kelas') == 'Kelas 4' ? 'selected' : '' }}>Kelas 4</option>
                                <option value="Kelas 5" {{ old('kelas') == 'Kelas 5' ? 'selected' : '' }}>Kelas 5</option>
                                <option value="Kelas 6" {{ old('kelas', 'Kelas 6') == 'Kelas 6' ? 'selected' : '' }}>Kelas 6</option>
                            </select>
                            @error('kelas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="L" {{ old('jenis_kelamin', 'L') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tahun Ajaran / Periode <span class="text-danger">*</span></label>
                        <input type="text" name="tahun_ajaran" class="form-control @error('tahun_ajaran') is-invalid @enderror" value="{{ old('tahun_ajaran', '2024/2025') }}" placeholder="Format: 2024/2025" required>
                        @error('tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Data Siswa
                        </button>
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

