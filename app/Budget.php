<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'budgets';

    public function accountSectors()
    {
        return $this->belongsTo('App\AccountSector', 'account_sector_id');
    }
}
