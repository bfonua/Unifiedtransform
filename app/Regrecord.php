<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regrecord extends Model
{
    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function section()
    {
        return $this->belongsTo(\App\Section::class);
    }

    public function house()
    {
        return $this->belongsTo(\App\House::class);
    }

    public function channel()
    {
        return $this->belongsTo(\App\FeeChannel::class, 'fee_id');
    }
}
