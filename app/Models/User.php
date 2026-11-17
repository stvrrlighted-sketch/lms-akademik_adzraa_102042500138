<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const DAFTAR_PERAN = ['admin', 'dosen', 'mahasiswa'];

    public const DOMAIN_EMAIL = [
        'admin' => 'admin.telkomuniversity.ac.id',
        'dosen' => 'dosen.telkomuniversity.ac.id',
        'mahasiswa' => 'student.telkomuniversity.ac.id',
    ];

    public static function buatEmail(string $username, string $peran): string
    {
        return $username.'@'.self::DOMAIN_EMAIL[$peran];
    }

    public function username(): string
    {
        return Str::before($this->email, '@');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function kelasDiampu(): HasMany
    {
        return $this->hasMany(Kelas::class, 'dosen_id');
    }

    public function kelasDiikuti(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'pendaftaran_kelas', 'mahasiswa_id', 'kelas_id');
    }
}
