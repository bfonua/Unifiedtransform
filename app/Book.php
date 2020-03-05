<?php

namespace App;

class Book extends Model
{
    protected $fillable = [
        'title', 'book_code', 'author', 'quantity', 'rackNo', 'rowNo', 'type',
        'about', 'price', 'img_path', 'class_id', 'school_id', 'user_id',
    ];

    public function school()
    {
        return $this->belongsTo(\App\School::class);
    }

    public function class()
    {
        return $this->belongsTo(\App\Myclass::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function issuedbook()
    {
        return $this->hasMany(\App\Issuedbook::class, 'book_id');
    }
}
