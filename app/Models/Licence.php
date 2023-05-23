<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licence extends Model
{
    use HasFactory;
    protected $table ="licences";
    public $timestamps = false;
    public function utilisateur(){
        return $this->hasOne("App\Models\Utilisateur");
    }
    public function bordereau(){
        return $this->hasOne("App\Models\Bordereau");
    }
    public function equipement(){
        return $this->hasOne("App\Models\Equipement");
    }
}