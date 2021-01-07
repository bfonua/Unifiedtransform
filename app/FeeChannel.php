<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeChannel extends Model
{
    protected $fillable = [
        'name',
        'active',
        'notes',
        'session',
    ];
}
