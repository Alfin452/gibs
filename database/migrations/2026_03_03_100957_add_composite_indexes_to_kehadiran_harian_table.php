<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kehadiran_harian', function (Blueprint $table) {
            $table->index(['id_guru', 'tanggal'], 'idx_guru_tanggal');
            $table->index(['id_kelas', 'id_mapel', 'tanggal'], 'idx_kelas_mapel_tanggal');
            $table->index(['id_siswa', 'id_mapel', 'tanggal'], 'idx_siswa_mapel_tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('kehadiran_harian', function (Blueprint $table) {
            $table->dropIndex('idx_guru_tanggal');
            $table->dropIndex('idx_kelas_mapel_tanggal');
            $table->dropIndex('idx_siswa_mapel_tanggal');
        });
    }
};
