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
        Schema::create('sakit_siswa', function (Blueprint $table) {
            $table->integer('id_sakit', true); // Primary Key & Auto Increment
            $table->integer('id_siswa');
            $table->date('tanggal');
            $table->time('waktu_masuk');
            $table->time('waktu_keluar')->nullable();
            $table->enum('status_akhir', ['Masih Sakit', 'Kembali ke Kelas'])->default('Masih Sakit');
            $table->string('keterangan', 255)->nullable();
            $table->integer('created_by')->nullable();

            // Timestamps (created_at & updated_at)
            $table->timestamps();

            // Indexes
            $table->index('id_siswa', 'sakit_siswa_id_siswa_index');
            $table->index('created_by', 'sakit_siswa_created_by_index');

            // Foreign Key Constraints
            $table->foreign('id_siswa', 'sakit_siswa_id_siswa_foreign')
                ->references('id_siswa')
                ->on('siswa')
                ->onDelete('cascade');

            // Opsional: Jika created_by merujuk ke tabel users
            $table->foreign('created_by', 'sakit_siswa_created_by_foreign')
                ->references('id_user')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sakit_siswa');
    }
};
