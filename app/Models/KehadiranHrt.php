<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KehadiranHrt extends Model
{
    use HasFactory;

    protected $table = 'kehadiran_hrt';

    protected $fillable = [
        'id_siswa',
        'id_kelas',
        'id_guru',
        'id_tahun_ajar',
        'tanggal',
        'status',
        'keterangan'
    ];



    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function tahunAjar()
    {
        return $this->belongsTo(TahunAjar::class, 'id_tahun_ajar', 'id_tahun_ajar');
    }
}
