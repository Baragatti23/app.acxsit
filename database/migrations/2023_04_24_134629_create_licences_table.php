<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateLicencesTable extends Migration{
    public function up(){
        Schema::create('licences', function (Blueprint $table) {
            $table->String('reference_licence',15)->primary();
            $table->integer('mois_licence');
            $table->dateTime("created_at_licence")->default(date("Y-m-d H:i:s"));
            $table->dateTime("updated_at_licence")->default(date("Y-m-d H:i:s"));
            $table->string("reference_bordereau",15);
            $table->string("reference_equipement",15);
            $table->string("reference_utilisateur",15);
            $table->foreign("reference_bordereau")->references("reference_bordereau")->on("bordereaus")->onDelete('cascade');
            $table->foreign("reference_equipement")->references("reference_equipement")->on("equipements")->onDelete('cascade');
            // $table->foreign("reference_utilisateur")
            //     ->references("reference_utilisateur")
            //     ->on("utilisateurs")
            //     ->onDelete('null || set null');
        });
    }
    public function down(){
        Schema::dropIfExists('licences');
    }
}
