<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_presensi', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('pertemuan_id')->unique()->constrained('pertemuan')->cascadeOnDelete();
            $tabel->boolean('dibuka')->default(false);
            $tabel->timestamps();
        });

        Schema::create('presensi', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('sesi_presensi_id')->constrained('sesi_presensi')->cascadeOnDelete();
            $tabel->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $tabel->string('status', 10);
            $tabel->timestamps();
            $tabel->unique(['sesi_presensi_id', 'mahasiswa_id']);
        });

        Schema::create('nilai_kelas', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $tabel->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $tabel->unsignedTinyInteger('uts')->nullable();
            $tabel->unsignedTinyInteger('uas')->nullable();
            $tabel->text('umpan_balik')->nullable();
            $tabel->timestamps();
            $tabel->unique(['kelas_id', 'mahasiswa_id']);
        });

        Schema::create('berita', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->string('judul', 150);
            $tabel->text('isi');
            $tabel->date('tanggal_terbit');
            $tabel->date('kedaluwarsa_pada')->nullable();
            $tabel->string('banner')->nullable();
            $tabel->foreignId('pembuat_id')->nullable()->constrained('users')->nullOnDelete();
            $tabel->timestamps();
        });

        Schema::create('permintaan_reset_kata_sandi', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $tabel->string('status', 12)->default('menunggu');
            $tabel->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_reset_kata_sandi');
        Schema::dropIfExists('berita');
        Schema::dropIfExists('nilai_kelas');
        Schema::dropIfExists('presensi');
        Schema::dropIfExists('sesi_presensi');
    }
};
