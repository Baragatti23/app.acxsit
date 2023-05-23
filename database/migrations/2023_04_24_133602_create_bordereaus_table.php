<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateBordereausTable extends Migration{
    public function up(){
        Schema::create('bordereaus', function (Blueprint $table) {
            $table->String('reference_bordereau',15)->primary();
            $table->String('nom_livreur_bordereau',100);
            $table->String('nom_recepteur_bordereau',100);
            $table->String('contacte_livreur_bordereau',255);
            $table->String('contacte_recepteur_bordereau',255);
            $table->dateTime("created_at_bordereau")->default(date("Y-m-d H:i:s"));
            $table->dateTime("updated_at_bordereau")->default(date("Y-m-d H:i:s"));
            $table->string("reference_proforma",15);
            $table->string("reference_utilisateur",15);
            $table->foreign("reference_proforma")->references("reference_proforma")->on("proformas")->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign("reference_utilisateur")
            //     ->references("reference_utilisateur")
            //     ->on("utilisateurs")
            //     ->onDelete('null || set null');
        });
    }
    public function down(){
        Schema::dropIfExists('bordereaus');
    }
}
