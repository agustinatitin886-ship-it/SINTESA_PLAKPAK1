<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilWaspas extends Model
{
    use HasFactory;

    protected $table = 'hasil_waspas';
    protected $primaryKey = 'id_hasil';

    protected $fillable = [
        'id_siswa',
        'periode',
        'nilai_wsm',
        'nilai_wpm',
        'nilai_qi',
        'peringkat',
        'peringkat_kelas',
        'status_kelayakan',
    ];

    protected $casts = [
        'nilai_wsm' => 'float',
        'nilai_wpm' => 'float',
        'nilai_qi' => 'float',
        'peringkat' => 'integer',
        'peringkat_kelas' => 'integer',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
}

