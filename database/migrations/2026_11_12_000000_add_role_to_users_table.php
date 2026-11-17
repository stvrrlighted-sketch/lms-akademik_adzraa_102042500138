<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $tabel) {
            // Peran bawaan adalah yang terendah agar akun baru tidak pernah otomatis menjadi admin.
            $tabel->string('role')->default('mahasiswa')->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $tabel) {
            $tabel->dropColumn('role');
        });
    }
};
