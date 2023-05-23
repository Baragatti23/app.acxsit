<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proforma extends Model
{
    use HasFactory;
    protected $table ="proformas";
    public $timestamps = false;
    public function utilisateur(){
        return $this->belongsTo("App\Models\Utilisateur","reference_utilisateur","reference_utilisateur");
    }
    public function client(){
        return $this->belongsTo("App\Models\Client","reference_client","reference_client");
    }
    public function calcules(){
        return $this->hasMany(Calcule::class, "reference_proforma","reference_proforma");
    }
}