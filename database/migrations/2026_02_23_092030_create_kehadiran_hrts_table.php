<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kehadiran_hrt', function (Blueprint $table) {
            $table->id();

            $table->integer('id_siswa');
            $table->integer('id_kelas');
            $table->integer('id_guru');
            $table->integer('id_tahun_ajar');

            $table->date('tanggal');
            $table->enum('status', ['H', 'S', 'I', 'A'])->default('H')->comment('H=Hadir, S=Sakit, I=Izin, A=Alpha');
            $table->string('keterangan', 255)->nullable()->comment('Alasan jika sakit/izin');
            $table->timestamps();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
            $table->foreign('id_tahun_ajar')->references('id_tahun_ajar')->on('tahun_ajar')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran_hrt');
    }
};
