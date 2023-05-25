<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;
    protected $table ="formations";
    public $timestamps = false;
    public function utilisateur(){
        return $this->hasOne("App\Models\Utilisateur");
    }
}