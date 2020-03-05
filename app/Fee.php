<?php

namespace App;

class Fee extends Model
{
    protected $fillable = ['school_id', 'fee_channel_id', 'fee_type_id', 'session'];

    public function fee_type()
    {
        return $this->belongsTo(\App\FeeType::class, 'fee_type_id');
    }

    public function fee_channel()
    {
        return $this->belongsTo(\App\FeeChannel::class, 'fee_channel_id');
    }

    public function assigns()
    {
        return $this->hasMany(\App\Assign::class);
    }
}
