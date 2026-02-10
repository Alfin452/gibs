<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KehadiranHarian extends Model
{
    protected $table = 'kehadiran_harian';
    protected $guarded = []; 

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}
