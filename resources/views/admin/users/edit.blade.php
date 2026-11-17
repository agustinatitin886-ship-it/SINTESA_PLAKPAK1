@extends('layouts.app')

@section('title', 'Ubah Pengguna')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Manajemen Pengguna
    </a>
    <h3 class="fw-bold text-dark mb-1">Ubah Pengguna: {{ $user->nama_pengguna }}</h3>
    <p class="text-muted mb-0">Perbarui profil atau kata sandi pengguna</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Formulir Ubah Pengguna</div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user->id_user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pengguna" class="form-control @error('nama_pengguna') is-invalid @enderror" value="{{ old('nama_pengguna', $user->nama_pengguna) }}" required>
                        @error('nama_pengguna')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Pengguna (Username) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alamat Surel (Email)</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kata Sandi Baru <span class="text-muted small fw-normal">(Kosongkan jika tidak ingin mengubah kata sandi)</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Biarkan kosong jika tidak diubah">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Peran / Hak Akses <span class="text-danger">*</span></label>
                            <select name="role" id="roleSelect" class="form-select @error('role') is-invalid @enderror" required onchange="toggleKelasField()">
                                <option value="walikelas" {{ old('role', $user->role) == 'walikelas' ? 'selected' : '' }}>Wali Kelas (Perekam Nilai)</option>
                                <option value="kepsek" {{ old('role', $user->role) == 'kepsek' ? 'selected' : '' }}>Kepala Sekolah (Pengambil Keputusan)</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator (Tata Usaha)</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6" id="kelasField">
                            <label class="form-label fw-semibold">Kelas Binaan (Khusus Wali Kelas)</label>
                            <select name="kelas_binaan" class="form-select @error('kelas_binaan') is-invalid @enderror">
                                <option value="">-- Pilih Kelas --</option>
                                <option value="Kelas 1" {{ old('kelas_binaan', $user->kelas_binaan) == 'Kelas 1' ? 'selected' : '' }}>Kelas 1</option>
                                <option value="Kelas 2" {{ old('kelas_binaan', $user->kelas_binaan) == 'Kelas 2' ? 'selected' : '' }}>Kelas 2</option>
                                <option value="Kelas 3" {{ old('kelas_binaan', $user->kelas_binaan) == 'Kelas 3' ? 'selected' : '' }}>Kelas 3</option>
                                <option value="Kelas 4" {{ old('kelas_binaan', $user->kelas_binaan) == 'Kelas 4' ? 'selected' : '' }}>Kelas 4</option>
                                <option value="Kelas 5" {{ old('kelas_binaan', $user->kelas_binaan) == 'Kelas 5' ? 'selected' : '' }}>Kelas 5</option>
                                <option value="Kelas 6" {{ old('kelas_binaan', $user->kelas_binaan) == 'Kelas 6' ? 'selected' : '' }}>Kelas 6</option>
                            </select>
                            @error('kelas_binaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleKelasField() {
    const role = document.getElementById('roleSelect').value;
    const field = document.getElementById('kelasField');
    if (role === 'walikelas') {
        field.style.display = 'block';
    } else {
        field.style.display = 'none';
    }
}
toggleKelasField();
</script>
@endsection

