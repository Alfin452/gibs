<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjar extends Model
{
    protected $table = 'tahun_ajar';

    protected $primaryKey = 'id_tahun_ajar';

    public $timestamps = false;

    protected $guarded = [];
}
