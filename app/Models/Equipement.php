<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    use HasFactory;
    protected $table ="equipements";
    public $timestamps = false;
    public function utilisateur(){
        return $this->hasOne("App\Models\Utilisateur");
    }
    public function fournisseur(){
        return $this->hasOne("App\Models\Fournisseur");
    }
    public function proforma(){
        return $this->belongsToMany(Proforma::class,'calcules');
    }
}