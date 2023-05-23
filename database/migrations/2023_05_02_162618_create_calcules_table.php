<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
    public function up(): void{
        Schema::create('calcules', function (Blueprint $table) {
            $table->String('reference_proforma',15);
            $table->String('reference_equipement',15);
            $table->double('discount_calcule');
            $table->double('gpl_montant_calcule');
            $table->double('gpl_pourcent_calcule');
            $table->double('transport_calcule');
            $table->double('douane_calcule');
            $table->double('marge_calcule');
            $table->dateTime("created_at_calcule")->default(date("Y-m-d H:i:s"));
            $table->dateTime("updated_at_calcule")->default(date("Y-m-d H:i:s"));
            $table->string("reference_utilisateur",15);
            $table->primary(['reference_proforma','reference_equipement']);
            $table->foreign("reference_equipement")->references("reference_equipement")->on("equipements")->onDelete('cascade');
            $table->foreign("reference_proforma")->references("reference_proforma")->on("proformas")->onDelete('cascade');
            // $table->foreign("reference_utilisateur")
            //     ->references("reference_utilisateur")
            //     ->on("utilisateurs")
            //     ->onDelete('null || set null');
        });
    }
    public function down(): void{
        Schema::dropIfExists('equipement_proforma');
    }
};
