<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calcule extends Model
{
    use HasFactory;
    protected $table ="calcules";
    public $timestamps = false;
    // protected $primaryKey = ['reference_proforma', 'reference_equipement','reference_utilisateur'];
    public function utilisateur(){
        return $this->belongsTo(Utilisateur::class,'reference_utilisateur','reference_utilisateur');
    }
    public function proforma(){
        return $this->belongsTo(Proforma::class,'reference_proforma','reference_proforma');
    }
    public function equipement(){
        return $this->belongsTo(Equipement::class,'reference_equipement','reference_equipement');
    }
    
    
}