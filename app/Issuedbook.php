<?php

namespace App;

class Issuedbook extends Model
{
    protected $table = 'issued_books';

    public function book()
    {
        return $this->belongsTo('App\Book');
    }
}
