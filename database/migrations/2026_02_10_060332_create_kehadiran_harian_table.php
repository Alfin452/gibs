<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran_harian', function (Blueprint $table) {
            $table->id(); 
            $table->integer('id_siswa')->index();
            $table->integer('id_kelas')->index();
            $table->integer('id_mapel')->index();
            $table->integer('id_guru')->index();
            $table->integer('id_tahun_ajar')->index(); 
            // Data Inti
            $table->date('tanggal'); 
            $table->enum('status', ['H', 'S', 'I', 'A', 'L'])->default('H')->comment('H=Hadir, S=Sakit, I=Izin, A=Alpha, L=Libur/Guru Berhalangan');
            $table->string('keterangan')->nullable(); 

            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran_harian');
    }
};
