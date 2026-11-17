@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Pengguna</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Manajemen Akun Pengguna</h4>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
    </a>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Akun Pengguna Sistem</span>
        <span class="small text-muted">Total: {{ $users->count() }} Pengguna</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th>Nama Lengkap</th>
                    <th>Nama Pengguna (Username)</th>
                    <th>Peran / Hak Akses</th>
                    <th>Kelas Binaan</th>
                    <th>Alamat Email</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $item)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td class="fw-medium text-dark">
                            {{ $item->nama_pengguna }}
                            @if(Auth::id() === $item->id_user)
                                <span class="badge bg-light text-dark border ms-1" style="font-size: 10px;">Anda</span>
                            @endif
                        </td>
                        <td><code>{{ $item->username }}</code></td>
                        <td>
                            @if($item->role === 'admin')
                                <span class="badge bg-secondary-subtle text-secondary border">Administrator</span>
                            @elseif($item->role === 'walikelas')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Wali Kelas</span>
                            @elseif($item->role === 'kepsek')
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle">Kepala Sekolah</span>
                            @endif
                        </td>
                        <td>
                            @if($item->kelas_binaan)
                                <span class="badge bg-light text-dark border">{{ $item->kelas_binaan }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td><small class="text-muted">{{ $item->email ?? '-' }}</small></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.edit', $item->id_user) }}" class="btn btn-outline-secondary" title="Ubah Akun">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(Auth::id() !== $item->id_user)
                                    <form action="{{ route('admin.users.destroy', $item->id_user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $item->nama_pengguna }}?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Akun">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada akun pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
