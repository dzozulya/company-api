<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'version',
        'name',
        'edrpou',
        'address',
        'created_at'
    ];
}
