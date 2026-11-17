<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_studi', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->string('kode', 20)->unique();
            $tabel->string('nama');
            $tabel->timestamps();
        });

        Schema::create('tahun_ajaran', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->string('nama', 100)->unique();
            $tabel->boolean('aktif')->default(false);
            $tabel->timestamps();
        });

        Schema::create('mata_kuliah', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->string('kode', 20)->unique();
            $tabel->string('nama');
            $tabel->unsignedTinyInteger('sks');
            $tabel->foreignId('program_studi_id')->nullable()->constrained('program_studi')->nullOnDelete();
            $tabel->timestamps();
        });

        Schema::create('kelas', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->restrictOnDelete();
            $tabel->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->restrictOnDelete();
            $tabel->string('nama', 20);
            $tabel->foreignId('dosen_id')->constrained('users')->restrictOnDelete();
            $tabel->unsignedTinyInteger('bobot_tugas')->default(30);
            $tabel->unsignedTinyInteger('bobot_uts')->default(30);
            $tabel->unsignedTinyInteger('bobot_uas')->default(40);
            $tabel->timestamps();
            $tabel->unique(['mata_kuliah_id', 'tahun_ajaran_id', 'nama']);
        });

        Schema::create('pendaftaran_kelas', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $tabel->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $tabel->timestamps();
            $tabel->unique(['kelas_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_kelas');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('mata_kuliah');
        Schema::dropIfExists('tahun_ajaran');
        Schema::dropIfExists('program_studi');
    }
};
