<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalKepsek extends Model
{
    use HasFactory;

    protected $table = 'approval_kepsek';
    protected $primaryKey = 'id_approval';

    protected $fillable = [
        'periode',
        'status',
        'nomor_sk',
        'catatan',
        'tanggal_persetujuan',
        'disetujui_oleh',
    ];

    protected $casts = [
        'tanggal_persetujuan' => 'datetime',
    ];

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh', 'id_user');
    }

    public function isApproved(): bool
    {
        return $this->status === 'disetujui';
    }
}

