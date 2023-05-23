<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table ="clients";
    public $timestamps = false;
    public function utilisateur(){
        return $this->belongsTo("App\Models\Utilisateur",'reference_utilisateur','reference_utilisateur');
    }
}