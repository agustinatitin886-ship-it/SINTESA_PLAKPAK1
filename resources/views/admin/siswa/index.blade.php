@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data Siswa</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Data Induk Siswa (Kandidat Alternatif)</h4>
    </div>
    <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Tambah Siswa
    </a>
</div>

<!-- Filter Toolbar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.siswa.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="kelas" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">-- Semua Kelas --</option>
                    @foreach($daftarKelas as $k)
                        <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select name="tahun_ajaran" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all">-- Semua Periode --</option>
                    @foreach($daftarTahunAjaran as $ta)
                        <option value="{{ $ta }}" {{ request('tahun_ajaran') == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau NISN siswa..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>

            <div class="col-md-2">
                <a href="{{ route('admin.siswa.index') }}" class="btn btn-sm btn-light border w-100">Atur Ulang</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Siswa (Total: {{ $siswa->total() }})</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th style="width: 140px;">NISN</th>
                    <th>Nama Lengkap</th>
                    <th style="width: 90px;">Kelas</th>
                    <th style="width: 70px;" class="text-center">L/P</th>
                    <th>Tahun Ajaran</th>
                    <th class="text-center">Status Nilai</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswa as $index => $item)
                    @php
                        $graded = $item->penilaian->count();
                        $complete = ($totalKriteria > 0 && $graded >= $totalKriteria);
                    @endphp
                    <tr>
                        <td class="text-center text-muted">{{ $siswa->firstItem() + $index }}</td>
                        <td><code>{{ $item->nisn }}</code></td>
                        <td class="fw-semibold text-dark">{{ $item->nama_lengkap }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $item->kelas }}</span></td>
                        <td class="text-center">
                            @if($item->jenis_kelamin === 'L')
                                <span class="badge bg-light text-primary border">L</span>
                            @else
                                <span class="badge bg-light text-danger border">P</span>
                            @endif
                        </td>
                        <td>{{ $item->tahun_ajaran }}</td>
                        <td class="text-center">
                            @if($complete)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    Lengkap ({{ $graded }}/{{ $totalKriteria }})
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle">
                                    Belum ({{ $graded }}/{{ $totalKriteria }})
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.siswa.edit', $item->id_siswa) }}" class="btn btn-outline-secondary" title="Ubah Siswa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.siswa.destroy', $item->id_siswa) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa {{ $item->nama_lengkap }}?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus Siswa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Tidak ditemukan data siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($siswa->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $siswa->links() }}
        </div>
    @endif
</div>
@endsection
