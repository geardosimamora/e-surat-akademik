<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterCounter extends Model
{
    public $timestamps = false; // Karena di migration kita tidak pakai timestamps()

    protected $fillable = [
        'year',
        'month',
        'last_number',
    ];
}