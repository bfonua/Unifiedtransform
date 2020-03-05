<?php

namespace App;

class Payment extends Model
{
    public function fees()
    {
        return $this->belongsTo(\App\Fee::class, 'fee_id');
    }
}
