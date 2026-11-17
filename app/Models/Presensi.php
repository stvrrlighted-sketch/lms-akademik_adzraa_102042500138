<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Presensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = ['sesi_presensi_id', 'mahasiswa_id', 'status'];

    public const DAFTAR_STATUS = ['Hadir', 'Izin', 'Sakit', 'Alpa'];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(SesiPresensi::class, 'sesi_presensi_id');
    }
}
