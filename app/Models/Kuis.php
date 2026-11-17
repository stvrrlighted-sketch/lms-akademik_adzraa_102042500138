<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kuis extends Model
{
    protected $table = 'kuis';

    protected $fillable = ['kelas_id', 'judul', 'tenggat'];

    protected function casts(): array
    {
        return ['tenggat' => 'datetime'];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function soal(): HasMany
    {
        return $this->hasMany(KuisSoal::class);
    }

    public function percobaan(): HasMany
    {
        return $this->hasMany(KuisPercobaan::class);
    }
}
