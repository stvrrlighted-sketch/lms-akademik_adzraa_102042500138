<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['mata_kuliah_id', 'tahun_ajaran_id', 'nama', 'dosen_id', 'bobot_tugas', 'bobot_uts', 'bobot_uas'];

    public function label(): string
    {
        return $this->mataKuliah->kode.' '.$this->mataKuliah->nama.', '.__('Kelas').' '.$this->nama;
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function mahasiswa(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pendaftaran_kelas', 'kelas_id', 'mahasiswa_id');
    }

    public function pertemuan(): HasMany
    {
        return $this->hasMany(Pertemuan::class);
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class);
    }

    public function kuis(): HasMany
    {
        return $this->hasMany(Kuis::class);
    }

    public function nilaiKelas(): HasMany
    {
        return $this->hasMany(NilaiKelas::class);
    }

    public function scopeDapatDikelolaOleh(Builder $kueri, User $pengguna): Builder
    {
        // Dosen hanya boleh mengelola kelas yang ia ampu; peran lain selain admin mendapat hasil kosong (gagal tertutup).
        return match ($pengguna->role) {
            'admin' => $kueri,
            'dosen' => $kueri->where('dosen_id', $pengguna->id),
            default => $kueri->whereRaw('1 = 0'),
        };
    }

    public function scopeDapatDiikutiOleh(Builder $kueri, User $pengguna): Builder
    {
        // Hanya mahasiswa yang terdaftar yang lolos; peran lain mendapat hasil kosong.
        return match ($pengguna->role) {
            'mahasiswa' => $kueri->whereHas('mahasiswa', fn (Builder $sub) => $sub->where('users.id', $pengguna->id)),
            default => $kueri->whereRaw('1 = 0'),
        };
    }
}
