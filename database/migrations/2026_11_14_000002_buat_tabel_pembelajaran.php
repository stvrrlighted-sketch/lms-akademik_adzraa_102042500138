<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertemuan', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $tabel->unsignedTinyInteger('nomor');
            $tabel->string('topik', 200)->nullable();
            $tabel->timestamps();
            $tabel->unique(['kelas_id', 'nomor']);
        });

        Schema::create('materi', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('pertemuan_id')->constrained('pertemuan')->cascadeOnDelete();
            $tabel->string('jenis', 10);
            $tabel->string('judul', 150);
            $tabel->string('path')->nullable();
            $tabel->string('nama_asli')->nullable();
            $tabel->string('url', 500)->nullable();
            $tabel->timestamps();
        });

        Schema::create('tugas', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $tabel->string('judul', 150);
            $tabel->text('instruksi');
            $tabel->dateTime('tenggat');
            $tabel->timestamps();
        });

        Schema::create('pengumpulan_tugas', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $tabel->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $tabel->string('path');
            $tabel->string('nama_asli');
            $tabel->dateTime('dikumpulkan_pada');
            $tabel->string('status', 12);
            $tabel->unsignedTinyInteger('skor')->nullable();
            $tabel->text('umpan_balik')->nullable();
            $tabel->timestamps();
            $tabel->unique(['tugas_id', 'mahasiswa_id']);
        });

        Schema::create('kuis', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $tabel->string('judul', 150);
            $tabel->dateTime('tenggat')->nullable();
            $tabel->timestamps();
        });

        Schema::create('kuis_soal', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('kuis_id')->constrained('kuis')->cascadeOnDelete();
            $tabel->string('jenis', 15);
            $tabel->text('pertanyaan');
            $tabel->json('opsi')->nullable();
            $tabel->string('kunci', 1)->nullable();
            $tabel->timestamps();
        });

        Schema::create('kuis_percobaan', function (Blueprint $tabel) {
            $tabel->id();
            $tabel->foreignId('kuis_id')->constrained('kuis')->cascadeOnDelete();
            $tabel->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $tabel->json('jawaban');
            $tabel->unsignedTinyInteger('skor')->nullable();
            $tabel->text('umpan_balik')->nullable();
            $tabel->dateTime('selesai_pada');
            $tabel->timestamps();
            $tabel->unique(['kuis_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuis_percobaan');
        Schema::dropIfExists('kuis_soal');
        Schema::dropIfExists('kuis');
        Schema::dropIfExists('pengumpulan_tugas');
        Schema::dropIfExists('tugas');
        Schema::dropIfExists('materi');
        Schema::dropIfExists('pertemuan');
    }
};
