<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'isd_no',
        'dialing_code',
        'status',
        'remark',
    ];
}
