<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateConnexionsTable extends Migration{
    public function up()
    {
        Schema::create('connexions', function (Blueprint $table) {
            $table->String('reference_connexion',15)->primary();
            $table->String('ip_connexion',80)->nullable();
            $table->String('etat_connexion',15)->nullable();
            $table->String('browser_connexion',60)->nullable();
            $table->String('os_connexion',60)->nullable();
            $table->text('user_agent_connexion')->nullable();
            $table->dateTime('date_closed_connexion')->nullable();
            $table->dateTime("created_at_connexion")->default(date("Y-m-d H:i:s"));
            $table->dateTime("updated_at_connexion")->default(date("Y-m-d H:i:s"));
            $table->String("reference_utilisateur",15);
            // $table->foreign("reference_utilisateur")
            //     ->references("reference_utilisateur")
            //     ->on("utilisateurs")
            //     ->onDelete('null || set null');
        });
    }
    public function down(){
        Schema::dropIfExists('connexions');
    }
}
