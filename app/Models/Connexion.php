<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connexion extends Model
{
    use HasFactory;
    protected $table ="connexions";
    public $timestamps = false;
    protected $fillable = [
        'reference_connexion',
        'etat_connexion',
        'browser_connexion',
        'os_connexion',
        'user_agent_connexion',
        'date_closed_connexion',
        'created_at_connexion',
        'updated_at_connexion',
        'reference_utilisateur',
        'token_connexion'
    ];
    public function utilisateur(){
        return $this->hasOne("App\Models\Utilisateur");
    }
}