<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bordereau extends Model
{
    use HasFactory;
    protected $table ="bordereaus";
    public $timestamps = false;
    public function proforma(){
        return $this->hasOne("App\Models\Proforma");
    }
    public function utilisateur(){
        return $this->hasOne("App\Models\Utilisateur");
    }
}