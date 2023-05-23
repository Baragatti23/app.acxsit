<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
    public function up(): void{
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->string("reference_utilisateur",15)->primary();
            $table->string("name_utilisateur",30);
            $table->string("lastname_utilisateur",50);
            $table->string("email_utilisateur",255);
            $table->string("password_utilisateur",255);
            $table->dateTime("created_at_utilisateur");
            $table->dateTime("updated_at_utilisateur");
            $table->string("reference_statu",15);
            $table->string("reference_profil",15);
            $table->string("created_by_utilisateur")->nullable();
            $table->foreign("reference_statu")->references("reference_statu")->on("status");
            $table->foreign("reference_profil")->references("reference_profil")->on("profils");
        });
    }
    public function down(): void{
        Schema::dropIfExists('utilisateurs');
    }
};
