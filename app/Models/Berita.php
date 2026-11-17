<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Berita extends Model
{
    public const BANNER_LEBAR_MAKS = 1280;

    public const BANNER_TINGGI_MAKS = 720;

    protected $table = 'berita';

    protected $fillable = ['judul', 'isi', 'tanggal_terbit', 'kedaluwarsa_pada', 'banner', 'pembuat_id'];

    protected function casts(): array
    {
        return ['tanggal_terbit' => 'date', 'kedaluwarsa_pada' => 'date'];
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function scopeTerbit(Builder $kueri): Builder
    {
        return $kueri->whereDate('tanggal_terbit', '<=', today());
    }

    public function scopeBelumKedaluwarsa(Builder $kueri): Builder
    {
        return $kueri->where(function (Builder $sub) {
            $sub->whereNull('kedaluwarsa_pada')->orWhereDate('kedaluwarsa_pada', '>=', today());
        });
    }

    public function scopeDapatDilihatOleh(Builder $kueri, User $pengguna): Builder
    {
        // Berita kedaluwarsa hanya terlihat oleh admin dan dosen; mahasiswa hanya melihat berita yang terbit dan belum kedaluwarsa.
        return match ($pengguna->role) {
            'admin' => $kueri,
            'dosen' => $kueri->terbit(),
            default => $kueri->terbit()->belumKedaluwarsa(),
        };
    }

    public function kedaluwarsa(): bool
    {
        return $this->kedaluwarsa_pada !== null && $this->kedaluwarsa_pada->lt(today());
    }

    public function terjadwal(): bool
    {
        return $this->tanggal_terbit->gt(today());
    }

    public function adaBanner(): bool
    {
        return $this->banner !== null && is_file(public_path('uploads/berita/'.$this->banner));
    }

    public function urlBanner(): ?string
    {
        return $this->adaBanner() ? asset('uploads/berita/'.$this->banner) : null;
    }

    public function ukuranBanner(): ?array
    {
        if (! $this->adaBanner()) {
            return null;
        }

        $ukuran = @getimagesize(public_path('uploads/berita/'.$this->banner));

        return $ukuran ? [$ukuran[0], $ukuran[1]] : null;
    }
}
