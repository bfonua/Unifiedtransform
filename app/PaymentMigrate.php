<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMigrate extends Model
{
    protected $table = 'paymentmigrate';
    // protected $fillable = array('student_id');
    protected $primaryKey = 'pay_id';
    public $timestamps = false;


}
