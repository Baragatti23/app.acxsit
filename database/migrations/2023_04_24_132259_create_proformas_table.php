<?php

use App\Models\Equipement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateProformasTable extends Migration{
    public function up(){
        Schema::create('proformas', function (Blueprint $table) {
            $table->String('reference_proforma',15)->primary();
            $table->String('sujet_proforma',80);
            $table->date('date_livraison_proforma');
            $table->integer('garantie_proforma');
            $table->double('tva_proforma');
            $table->double('totalht_proforma');
            $table->double('totalttc_proforma');
            $table->dateTime("created_at_proforma")->default(date("Y-m-d H:i:s"));
            $table->dateTime("updated_at_proforma")->default(date("Y-m-d H:i:s"));
            $table->string("reference_client",15);
            $table->string("reference_utilisateur",15);
            $table->foreign("reference_client")->references("reference_client")->on("clients");
            // $table->foreign("reference_utilisateur")
            //     ->references("reference_utilisateur")
            //     ->on("utilisateurs")
            //     ->onDelete('null || set null');
        });
    }
    public function down(){
        Schema::dropIfExists('proformas');
    }
}
