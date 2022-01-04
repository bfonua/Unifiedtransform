<?php

namespace App;

use App\Model;
// use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    /**
     * Gets the associated users for each hosue
     * 
     */
    public function students()
    {
        return $this->hasMany('App\StudentInfo', 'house_id');
    }

    public function users()
    {
        return $this->hasManyDeep(
            'App\User', ['App\StudentInfo'],
            [
                'house_id',
                'id'
            ],
            [
                'id', 
                'student_id'
            ]
        )->where('session', now()->year);
    }
    
}
