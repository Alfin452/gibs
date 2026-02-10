<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjar extends Model
{
    // 1. INI KUNCINYA: Paksa Laravel pakai nama tabel tanpa 's'
    protected $table = 'tahun_ajar';

    // 2. Sesuaikan Primary Key (biasanya id_tahun_ajar)
    protected $primaryKey = 'id_tahun_ajar';

    // 3. Matikan timestamps jika tabel ini tidak punya kolom created_at/updated_at
    public $timestamps = false;

    // 4. Izinkan kolom apa saja yang boleh diambil (opsional tapi aman)
    protected $guarded = [];
}
