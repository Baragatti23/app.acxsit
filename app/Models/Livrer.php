<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livrer extends Model
{
    use HasFactory;
    protected $table ="livrers";
    public $timestamps = false;
}