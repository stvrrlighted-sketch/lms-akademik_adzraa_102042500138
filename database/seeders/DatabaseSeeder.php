<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Pertemuan;
use App\Models\ProgramStudi;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Akun uji memakai kata sandi umum, jadi seeder ini tidak boleh jalan di produksi.
        if (app()->environment('production')) {
            return;
        }

        $daftarAkun = [
            ['nama' => 'Admin Uji', 'username' => 'admin', 'peran' => 'admin'],
            ['nama' => 'Dosen Uji', 'username' => 'dosen', 'peran' => 'dosen'],
            ['nama' => 'Dosen Uji Dua', 'username' => 'dosen2', 'peran' => 'dosen'],
            ['nama' => 'Mahasiswa Uji', 'username' => 'mahasiswa', 'peran' => 'mahasiswa'],
            ['nama' => 'Mahasiswa Uji Dua', 'username' => 'mahasiswa2', 'peran' => 'mahasiswa'],
        ];

        $akun = [];

        foreach ($daftarAkun as $data) {
            $pengguna = User::firstOrNew(['email' => User::buatEmail($data['username'], $data['peran'])]);

            // Kolom role sengaja di luar #[Fillable], jadi hanya forceFill yang boleh menetapkannya.
            $pengguna->forceFill([
                'name' => $data['nama'],
                'password' => 'password',
                'role' => $data['peran'],
                'email_verified_at' => now(),
            ])->save();

            $akun[$data['username']] = $pengguna;
        }

        $prodi = ProgramStudi::firstOrCreate(['kode' => 'IF'], ['nama' => 'Informatika']);
        $tahun = TahunAjaran::firstOrCreate(['nama' => '2026/2027 Ganjil'], ['aktif' => true]);

        $daftarMataKuliah = [
            ['kode' => 'IF-PAW', 'nama' => 'Pemrograman Aplikasi Web', 'sks' => 3],
            ['kode' => 'IF-KKD', 'nama' => 'Keamanan Komunikasi Data', 'sks' => 3],
        ];

        foreach ($daftarMataKuliah as $data) {
            $mataKuliah = MataKuliah::firstOrCreate(['kode' => $data['kode']], [
                'nama' => $data['nama'],
                'sks' => $data['sks'],
                'program_studi_id' => $prodi->id,
            ]);

            $kelas = Kelas::firstOrCreate(
                ['mata_kuliah_id' => $mataKuliah->id, 'tahun_ajaran_id' => $tahun->id, 'nama' => 'A'],
                ['dosen_id' => $akun['dosen']->id],
            );

            foreach (range(1, 14) as $nomor) {
                Pertemuan::firstOrCreate(['kelas_id' => $kelas->id, 'nomor' => $nomor]);
            }

            Pertemuan::where('kelas_id', $kelas->id)->where('nomor', 1)->whereNull('topik')->update(['topik' => 'Pengantar mata kuliah']);

            $kelas->mahasiswa()->syncWithoutDetaching([$akun['mahasiswa']->id]);
        }

        Berita::firstOrCreate(['judul' => 'Selamat datang di LMS'], [
            'isi' => 'Gunakan akun yang diberikan admin untuk masuk. Materi, tugas, kuis, presensi, dan nilai tersedia pada menu Kelas saya.',
            'tanggal_terbit' => today(),
            'pembuat_id' => $akun['admin']->id,
        ]);
    }
}
