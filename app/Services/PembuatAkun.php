<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class PembuatAkun
{
    public function buatBanyak(array $daftarBaris, string $peran): array
    {
        $hasil = [];

        foreach ($daftarBaris as $baris) {
            $nama = trim($baris['nama'] ?? '');

            if ($nama === '') {
                continue;
            }

            $usernameDiminta = strtolower(trim($baris['username'] ?? ''));
            $kataSandi = trim($baris['password'] ?? '') ?: Str::password(10, symbols: false, spaces: false);

            if ($usernameDiminta !== '') {
                $email = User::buatEmail($usernameDiminta, $peran);

                if (User::where('email', $email)->exists()) {
                    $hasil[] = ['nama' => $nama, 'email' => $email, 'password' => '', 'status' => 'Dilewati', 'alasan' => 'Username sudah dipakai.'];

                    continue;
                }
            } else {
                $email = $this->emailUnik($nama, $peran);
            }

            // role ditetapkan lewat forceFill karena sengaja tidak ada di #[Fillable] pada model User.
            (new User)->forceFill([
                'name' => $nama,
                'email' => $email,
                'password' => $kataSandi,
                'role' => $peran,
                'email_verified_at' => now(),
            ])->save();

            $hasil[] = ['nama' => $nama, 'email' => $email, 'password' => $kataSandi, 'status' => 'Dibuat', 'alasan' => ''];
        }

        return $hasil;
    }

    private function emailUnik(string $nama, string $peran): string
    {
        $dasar = Str::of($nama)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', '')->toString() ?: 'pengguna';
        $username = $dasar;
        $urutan = 1;

        while (User::where('email', User::buatEmail($username, $peran))->exists()) {
            $urutan++;
            $username = $dasar.$urutan;
        }

        return User::buatEmail($username, $peran);
    }
}
