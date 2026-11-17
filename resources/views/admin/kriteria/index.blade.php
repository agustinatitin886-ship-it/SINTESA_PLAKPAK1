@extends('layouts.app')

@section('title', 'Kriteria Penilaian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kriteria Penilaian</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Data Parameter Kriteria Penilaian</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.bwm.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-sliders me-1"></i> Hitung Bobot BWM
        </a>
        <a href="{{ route('admin.kriteria.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kriteria
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Kriteria Penilaian Siswa Berprestasi</span>
        <span class="small text-muted">
            Total Bobot: <strong>{{ round($totalBobot * 100, 2) }}%</strong>
            @if(abs($totalBobot - 1.0) < 0.01)
                <span class="text-success ms-1"><i class="bi bi-check-circle-fill"></i> Sesuai 100%</span>
            @else
                <span class="text-warning ms-1"><i class="bi bi-exclamation-circle-fill"></i> Perlu Dihitung BWM</span>
            @endif
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;" class="text-center">No</th>
                    <th style="width: 100px;">Kode</th>
                    <th>Nama Kriteria</th>
                    <th>Jenis Atribut</th>
                    <th class="text-center" style="width: 140px;">Bobot ($w_j$)</th>
                    <th class="text-center" style="width: 140px;">Persentase</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kriteria as $index => $item)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td><span class="font-monospace fw-bold">{{ $item->kode_kriteria }}</span></td>
                        <td class="fw-medium text-dark">{{ $item->nama_kriteria }}</td>
                        <td>
                            @if(strcasecmp($item->jenis_atribut, 'Benefit') === 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    Benefit (Keuntungan)
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    Cost (Biaya / Pelanggaran)
                                </span>
                            @endif
                        </td>
                        <td class="text-center font-monospace">{{ number_format($item->bobot_bwm, 4) }}</td>
                        <td class="text-center font-monospace fw-bold text-success">{{ round($item->bobot_bwm * 100, 2) }}%</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.kriteria.edit', $item->id_kriteria) }}" class="btn btn-outline-secondary" title="Ubah Kriteria">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.kriteria.destroy', $item->id_kriteria) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria {{ $item->nama_kriteria }}?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus Kriteria">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data kriteria yang tersimpan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
