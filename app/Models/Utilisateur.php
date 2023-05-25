<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model{
    use HasFactory;
    protected $table ="utilisateurs";
    public $timestamps = false;
    protected $guarded = [];
    public function statu(){
        return $this->hasOne(Statu::class,'reference_statu','reference_statu');
    }
    public function profil(){
        return $this->hasOne("App\Models\Profil",'reference_profil','reference_profil');
    }
    public function clients(){
        return $this->hasMany(Client::class,'reference_utilisateur','reference_utilisateur');
    }
    public function proformas(){
        return $this->hasMany("App\Models\Proforma");
    }
    public function bordereaus(){
        return $this->hasMany("App\Models\Bordereau");
    }
    public function licences(){
        return $this->hasMany("App\Models\Licence");
    }
    public function fournisseurs(){
        return $this->hasMany("App\Models\Fournisseur");
    }
    public function equipements(){
        return $this->hasMany("App\Models\Equipement");
    }
    public function connexions(){
        return $this->hasMany("App\Models\Connexion");
    }
    public function Calcules(){
        return $this->hasMany("App\Models\Calcule");
    }
}