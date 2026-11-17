<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KuisSoal extends Model
{
    protected $table = 'kuis_soal';

    protected $fillable = ['kuis_id', 'jenis', 'pertanyaan', 'opsi', 'kunci'];

    protected function casts(): array
    {
        return ['opsi' => 'array'];
    }

    public function kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class);
    }
}
