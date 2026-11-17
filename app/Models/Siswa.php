<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'nisn',
        'nama_lengkap',
        'kelas',
        'jenis_kelamin',
        'tahun_ajaran',
    ];

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'id_siswa', 'id_siswa');
    }

    public function hasilWaspas(): HasMany
    {
        return $this->hasMany(HasilWaspas::class, 'id_siswa', 'id_siswa');
    }

    /**
     * Helper to get raw value for a specific kriteria
     */
    public function getNilaiKriteria(int $idKriteria): ?float
    {
        $penilaian = $this->penilaian->firstWhere('id_kriteria', $idKriteria);
        return $penilaian ? (float) $penilaian->nilai_mentah : null;
    }
}

