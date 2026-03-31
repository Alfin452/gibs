<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $table = 'major';

    protected $primaryKey = 'id_major';

    protected $guarded = [];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_major', 'id_major');
    }
}
