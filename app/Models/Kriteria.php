<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';
    protected $primaryKey = 'id_kriteria';

    protected $fillable = [
        'kode_kriteria',
        'nama_kriteria',
        'jenis_atribut',
        'bobot_bwm',
    ];

    protected $casts = [
        'bobot_bwm' => 'float',
    ];

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'id_kriteria', 'id_kriteria');
    }
}

