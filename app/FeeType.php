<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    
    public function fees()
    {
        return $this->hasMany('App\Fee')->where('fees.session', now()->year);
    }

    public function assigns()
    {
        //
    }


    public function assigned()
    {
        return $this->hasManyDeep(
            'App\Assign', ['App\Fee'],
            [
                'fee_type_id', // FeeType FK on Fees
                'fee_id' // Fees FK on Assign
            ],
            [
                'id', // LK on FeeType
                'id' // LK on Fees
            ]
        )->where('assigns.session', now()->year);
    }
}
