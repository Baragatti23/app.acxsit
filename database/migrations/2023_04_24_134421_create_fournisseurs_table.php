<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateFournisseursTable extends Migration{
    public function up(){
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->String('reference_fournisseur',15)->primary();
            $table->String('nom_fournisserur',80);
            $table->String('telephone_fournisseur',50);
            $table->String('email_fournisseur',255);
            $table->String('adresse_fournisseur',255);
            $table->dateTime("created_at_fournisseur")->default(date("Y-m-d H:i:s"));
            $table->dateTime("updated_at_fournisseur")->default(date("Y-m-d H:i:s"));
            $table->String("reference_utilisateur",15);
            // $table->foreign("reference_utilisateur")
            //     ->references("reference_utilisateur")
            //     ->on("utilisateurs")
            //     ->onDelete('null || set null');
        });
    }
    public function down(){
        Schema::dropIfExists('fournisseurs');
    }
}
