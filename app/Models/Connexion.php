<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connexion extends Model
{
    use HasFactory;
    protected $table ="connexions";
    public $timestamps = false;
    public function utilisateur(){
        return $this->hasOne("App\Models\Utilisateur");
    }
}