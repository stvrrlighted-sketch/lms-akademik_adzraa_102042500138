<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NilaiKelas extends Model
{
    protected $table = 'nilai_kelas';

    protected $fillable = ['kelas_id', 'mahasiswa_id', 'uts', 'uas', 'umpan_balik'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}
