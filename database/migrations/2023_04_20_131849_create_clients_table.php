<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateClientsTable extends Migration{
    public function up(){
        Schema::create('clients', function (Blueprint $table) {
            $table->String('reference_client',15)->primary();
            $table->String('email_client',255);
            $table->String('telephone_client',50);
            $table->String('name_client',60);
            $table->String('address_client',255);
            $table->dateTime("created_at_client")->default(date("Y-m-d H:i:s"));
            $table->dateTime("updated_at_client")->default(date("Y-m-d H:i:s"));
            $table->string("reference_statde",15)->nullable();
            $table->string("reference_utilisateur",15);
            // $table->foreign("reference_utilisateur")
            //     ->references("reference_utilisateur")
            //     ->on("utilisateurs")
            //     ->onDelete('null || set null');
        });
    }
    public function down(){
        Schema::dropIfExists('clients');
    }
}
