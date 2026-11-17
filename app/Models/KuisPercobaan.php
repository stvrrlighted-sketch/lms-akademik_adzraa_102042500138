<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KuisPercobaan extends Model
{
    protected $table = 'kuis_percobaan';

    protected $fillable = ['kuis_id', 'mahasiswa_id', 'jawaban', 'skor', 'umpan_balik', 'selesai_pada'];

    protected function casts(): array
    {
        return ['jawaban' => 'array', 'selesai_pada' => 'datetime'];
    }

    public function kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}
