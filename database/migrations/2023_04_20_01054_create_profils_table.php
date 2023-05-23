<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateProfilsTable extends Migration{
    public function up(){
        Schema::create('profils', function (Blueprint $table) {
            $table->String('reference_profil',15)->primary();
            $table->String('libelle_profil',60);
            $table->dateTime("created_at_profil")->default(date("Y-m-d H:i:s"));
            $table->dateTime("updated_at_profil")->default(date("Y-m-d H:i:s"));
        });
    }
    public function down(){
        Schema::dropIfExists('profils');
    }
}
