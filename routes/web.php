<?php

use App\Http\Controllers\AutentikasiController;
use App\Http\Controllers\BeritaBacaController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\DasborController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KelolaKelasController;
use App\Http\Controllers\KuisKelolaController;
use App\Http\Controllers\LupaKataSandiController;
use App\Http\Controllers\MahasiswaKelasController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\NilaiKelolaController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PertemuanController;
use App\Http\Controllers\PresensiKelolaController;
use App\Http\Controllers\TugasKelolaController;
use App\Http\Controllers\UnduhController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('beranda');
});

Route::get('/bahasa/{kode}', function (string $kode) {
    abort_unless(in_array($kode, \App\Http\Middleware\AturBahasa::DAFTAR_BAHASA, true), 404);

    session(['bahasa' => $kode]);

    return redirect()->back(302, [], '/')->withCookie(cookie()->forever('bahasa', $kode));
})->name('bahasa.ganti');

Route::view('/faq', 'faq')->name('faq');
Route::view('/tutorial', 'tutorial')->name('tutorial');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AutentikasiController::class, 'formMasuk'])->name('login');

    // Batasi percobaan login untuk menahan penebakan kata sandi.
    Route::post('/login', [AutentikasiController::class, 'masuk'])
        ->middleware('throttle:5,1')
        ->name('login.proses');

    Route::get('/lupa-kata-sandi', [LupaKataSandiController::class, 'form'])->name('lupa.form');

    // Dibatasi agar formulir permintaan tidak dipakai untuk membanjiri antrean admin.
    Route::post('/lupa-kata-sandi', [LupaKataSandiController::class, 'kirim'])
        ->middleware('throttle:5,1')
        ->name('lupa.kirim');
});

Route::post('/logout', [AutentikasiController::class, 'keluar'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', function () {
    $rutePerPeran = [
        'admin' => 'admin.dasbor',
        'dosen' => 'dosen.dasbor',
        'mahasiswa' => 'mahasiswa.dasbor',
    ];

    $namaRute = $rutePerPeran[auth()->user()->role] ?? null;

    // Peran tak dikenal ditolak, bukan dialihkan ke dasbor bawaan (gagal tertutup).
    abort_if($namaRute === null, 403, 'Anda tidak memiliki hak akses!');

    return redirect()->route($namaRute);
})->middleware('auth')->name('dashboard');

Route::get('/berita/{id}', [BeritaBacaController::class, 'tampil'])->where('id', '[0-9]+')->middleware('auth')->name('berita.tampil');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [DasborController::class, 'admin'])->name('admin.dasbor');

    Route::get('/admin/berita', [BeritaController::class, 'indeks'])->name('berita.indeks');
    Route::get('/admin/berita/buat', [BeritaController::class, 'buat'])->name('berita.buat');
    Route::post('/admin/berita', [BeritaController::class, 'simpan'])->name('berita.simpan');
    Route::get('/admin/berita/{berita}/ubah', [BeritaController::class, 'ubah'])->name('berita.ubah');
    Route::put('/admin/berita/{berita}', [BeritaController::class, 'perbarui'])->name('berita.perbarui');
    Route::delete('/admin/berita/{berita}', [BeritaController::class, 'hapus'])->name('berita.hapus');

    Route::get('/admin/pengguna', [PenggunaController::class, 'index'])->name('pengguna.kelola');
    Route::get('/admin/pengguna/buat', [PenggunaController::class, 'buat'])->name('pengguna.buat');
    Route::post('/admin/pengguna', [PenggunaController::class, 'simpan'])->name('pengguna.simpan');
    Route::get('/admin/pengguna/{pengguna}/ubah', [PenggunaController::class, 'ubah'])->name('pengguna.ubah');
    Route::put('/admin/pengguna/{pengguna}', [PenggunaController::class, 'perbarui'])->name('pengguna.perbarui');
    Route::patch('/admin/pengguna/{pengguna}/peran', [PenggunaController::class, 'ubahPeran'])->name('pengguna.peran');
    Route::delete('/admin/pengguna/{pengguna}', [PenggunaController::class, 'hapus'])->name('pengguna.hapus');
    Route::post('/admin/permintaan-reset/{permintaan}/abaikan', [PenggunaController::class, 'abaikanPermintaan'])->name('reset.abaikan');

    Route::prefix('/admin/master/{jenis}')->where(['jenis' => 'tahun-ajaran|program-studi|mata-kuliah', 'id' => '[0-9]+'])->group(function () {
        Route::get('/', [MasterController::class, 'indeks'])->name('master.indeks');
        Route::get('/buat', [MasterController::class, 'buat'])->name('master.buat');
        Route::post('/', [MasterController::class, 'simpan'])->name('master.simpan');
        Route::get('/{id}/ubah', [MasterController::class, 'ubah'])->name('master.ubah');
        Route::put('/{id}', [MasterController::class, 'perbarui'])->name('master.perbarui');
        Route::delete('/{id}', [MasterController::class, 'hapus'])->name('master.hapus');
    });

    Route::get('/admin/kelas', [KelasController::class, 'indeks'])->name('kelas.indeks');
    Route::get('/admin/kelas/buat', [KelasController::class, 'buat'])->name('kelas.buat');
    Route::post('/admin/kelas', [KelasController::class, 'simpan'])->name('kelas.simpan');
    Route::get('/admin/kelas/{kelas}/ubah', [KelasController::class, 'ubah'])->name('kelas.ubah');
    Route::put('/admin/kelas/{kelas}', [KelasController::class, 'perbarui'])->name('kelas.perbarui');
    Route::delete('/admin/kelas/{kelas}', [KelasController::class, 'hapus'])->name('kelas.hapus');
    Route::get('/admin/kelas/{kelas}/mahasiswa', [KelasController::class, 'mahasiswa'])->name('kelas.mahasiswa');
    Route::post('/admin/kelas/{kelas}/mahasiswa', [KelasController::class, 'daftarkan'])->name('kelas.daftarkan');
    Route::delete('/admin/kelas/{kelas}/mahasiswa/{mahasiswa}', [KelasController::class, 'keluarkan'])->name('kelas.keluarkan');
});

Route::middleware(['auth', 'role:dosen'])->group(function () {
    Route::get('/dosen', [DasborController::class, 'dosen'])->name('dosen.dasbor');
});

Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/mahasiswa', [DasborController::class, 'mahasiswa'])->name('mahasiswa.dasbor');

    Route::get('/kelas-saya', [MahasiswaKelasController::class, 'indeks'])->name('kelassaya.indeks');
    Route::get('/kelas-saya/tugas/{tugas}', [MahasiswaKelasController::class, 'tugas'])->name('kelassaya.tugas');
    Route::post('/kelas-saya/tugas/{tugas}', [MahasiswaKelasController::class, 'kumpulkan'])->name('kelassaya.kumpul');
    Route::get('/kelas-saya/kuis/{kuis}', [MahasiswaKelasController::class, 'kuis'])->name('kelassaya.kuis');
    Route::post('/kelas-saya/kuis/{kuis}', [MahasiswaKelasController::class, 'kerjakan'])->name('kelassaya.kerjakan');
    Route::post('/kelas-saya/presensi/{sesi}', [MahasiswaKelasController::class, 'hadir'])->name('kelassaya.hadir');
    Route::get('/kelas-saya/{kelas}', [MahasiswaKelasController::class, 'tampil'])->name('kelassaya.tampil');

    // Pintasan dari alamat lama agar matriks akses awal tetap berlaku.
    Route::redirect('/lihat-materi', '/kelas-saya');
    Route::redirect('/nilai-saya', '/kelas-saya');
});

Route::middleware(['auth', 'role:admin,dosen'])->prefix('kelola')->group(function () {
    Route::get('/kelas', [KelolaKelasController::class, 'indeks'])->name('kelola.indeks');
    Route::get('/kelas/{kelas}', [KelolaKelasController::class, 'tampil'])->name('kelola.tampil');

    Route::get('/kelas/{kelas}/materi', [PertemuanController::class, 'indeks'])->name('materi.indeks');
    Route::put('/pertemuan/{pertemuan}', [PertemuanController::class, 'perbarui'])->name('pertemuan.perbarui');
    Route::post('/pertemuan/{pertemuan}/materi', [PertemuanController::class, 'tambahMateri'])->name('materi.tambah');
    Route::delete('/materi/{materi}', [PertemuanController::class, 'hapusMateri'])->name('materi.hapus');

    Route::get('/kelas/{kelas}/tugas', [TugasKelolaController::class, 'indeks'])->name('tugas.indeks');
    Route::get('/kelas/{kelas}/tugas/buat', [TugasKelolaController::class, 'buat'])->name('tugas.buat');
    Route::post('/kelas/{kelas}/tugas', [TugasKelolaController::class, 'simpan'])->name('tugas.simpan');
    Route::get('/tugas/{tugas}', [TugasKelolaController::class, 'tampil'])->name('tugas.tampil');
    Route::get('/tugas/{tugas}/ubah', [TugasKelolaController::class, 'ubah'])->name('tugas.ubah');
    Route::put('/tugas/{tugas}', [TugasKelolaController::class, 'perbarui'])->name('tugas.perbarui');
    Route::delete('/tugas/{tugas}', [TugasKelolaController::class, 'hapus'])->name('tugas.hapus');
    Route::post('/tugas/{tugas}/nilai', [TugasKelolaController::class, 'nilai'])->name('tugas.nilai');

    Route::get('/kelas/{kelas}/kuis', [KuisKelolaController::class, 'indeks'])->name('kuis.indeks');
    Route::post('/kelas/{kelas}/kuis', [KuisKelolaController::class, 'simpan'])->name('kuis.simpan');
    Route::get('/kuis/{kuis}', [KuisKelolaController::class, 'tampil'])->name('kuis.tampil');
    Route::delete('/kuis/{kuis}', [KuisKelolaController::class, 'hapus'])->name('kuis.hapus');
    Route::post('/kuis/{kuis}/soal', [KuisKelolaController::class, 'tambahSoal'])->name('kuis.soal.tambah');
    Route::delete('/soal/{soal}', [KuisKelolaController::class, 'hapusSoal'])->name('kuis.soal.hapus');
    Route::post('/kuis/{kuis}/nilai', [KuisKelolaController::class, 'nilai'])->name('kuis.nilai');

    Route::get('/kelas/{kelas}/presensi', [PresensiKelolaController::class, 'indeks'])->name('presensi.indeks');
    Route::get('/kelas/{kelas}/presensi/ekspor', [PresensiKelolaController::class, 'ekspor'])->name('presensi.ekspor');
    Route::get('/pertemuan/{pertemuan}/presensi', [PresensiKelolaController::class, 'sesi'])->name('presensi.sesi');
    Route::post('/pertemuan/{pertemuan}/presensi', [PresensiKelolaController::class, 'simpan'])->name('presensi.simpan');
    Route::post('/pertemuan/{pertemuan}/presensi/buka', [PresensiKelolaController::class, 'buka'])->name('presensi.buka');
    Route::post('/pertemuan/{pertemuan}/presensi/tutup', [PresensiKelolaController::class, 'tutup'])->name('presensi.tutup');

    Route::get('/kelas/{kelas}/nilai', [NilaiKelolaController::class, 'indeks'])->name('nilai.indeks');
    Route::get('/kelas/{kelas}/nilai/ekspor', [NilaiKelolaController::class, 'ekspor'])->name('nilai.ekspor');
    Route::put('/kelas/{kelas}/bobot', [NilaiKelolaController::class, 'bobot'])->name('nilai.bobot');
    Route::post('/kelas/{kelas}/nilai', [NilaiKelolaController::class, 'simpan'])->name('nilai.simpan');
});

Route::middleware(['auth', 'role:admin,dosen'])->group(function () {
    // Pintasan dari alamat lama agar matriks akses awal tetap berlaku.
    Route::redirect('/kelola-materi', '/kelola/kelas');
    Route::redirect('/kelola-nilai', '/kelola/kelas');
});

Route::middleware('auth')->prefix('unduh')->group(function () {
    Route::get('/materi/{materi}', [UnduhController::class, 'materi'])->name('unduh.materi');
    Route::get('/pengumpulan/{pengumpulan}', [UnduhController::class, 'pengumpulan'])->name('unduh.pengumpulan');
});
