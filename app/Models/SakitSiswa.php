<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SakitSiswa extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel secara eksplisit
    protected $table = 'sakit_siswa';

    // Mendefinisikan primary key kustom
    protected $primaryKey = 'id_sakit';

    // Jika primary key bukan incrementing integer, set ke false (tapi di SQL Anda auto_increment)
    public $incrementing = true;

    /**
     * Kolom yang dapat diisi melalui mass assignment.
     */
    protected $fillable = [
        'id_siswa',
        'tanggal',
        'waktu_masuk',
        'waktu_keluar',
        'status_akhir',
        'keterangan',
        'created_by',
    ];

    /**
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    /**
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}
