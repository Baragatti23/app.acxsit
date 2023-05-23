<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateEquipementsTable extends Migration{
    public function up(){
        Schema::create('equipements', function (Blueprint $table) {
            $table->String('reference_equipement',15)->primary();
            $table->String('designation_equipement',80);
            $table->String('categorie_equipement',60)->nullable();
            $table->double('prix_vente_equipement')->nullable();
            $table->double('prix_achat_equipement')->nullable();
            $table->integer('stock_equipement')->nullable();
            $table->text('caracteristiques_equipement')->nullable();
            $table->dateTime("created_at_equipement")->default(date("Y-m-d H:i:s"));
            $table->dateTime("updated_at_equipement")->default(date("Y-m-d H:i:s"));
            $table->String("reference_fournisseur",15)->nullable();
            $table->String("reference_utilisateur",15);
            $table->foreign("reference_fournisseur")->references("reference_fournisseur")->on("fournisseurs");
            // $table->foreign("reference_utilisateur")
            //     ->references("reference_utilisateur")
            //     ->on("utilisateurs")
            //     ->onDelete('null || set null');
        });
    }
    public function down(){
        Schema::dropIfExists('equipements');
    }
}
