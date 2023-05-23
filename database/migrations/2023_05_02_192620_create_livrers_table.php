<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
    public function up(): void{
        Schema::create('livrers', function (Blueprint $table) {
            $table->String('reference_bordereau',15);
            $table->String('reference_equipement',15);
            $table->double('unites');
            $table->dateTime("created_at_livrer")->default(date("Y-m-d H:i:s"));
            $table->dateTime("updated_at_livrer")->default(date("Y-m-d H:i:s"));
            $table->String('reference_utilisateur',15);
            $table->foreign("reference_equipement")->references("reference_equipement")->on("equipements");
            $table->foreign("reference_bordereau")->references("reference_bordereau")->on("bordereaus");
            $table->primary(['reference_bordereau','reference_equipement']);
            // $table->foreign("reference_utilisateur")
            //     ->references("reference_utilisateur")
            //     ->on("utilisateurs")
            //     ->onDelete('null || set null');
        });
    }
    public function down(): void{
        Schema::dropIfExists('livrers');
    }
};