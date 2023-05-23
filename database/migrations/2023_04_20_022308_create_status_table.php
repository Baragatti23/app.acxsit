<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
    public function up(): void{
        Schema::create('status', function (Blueprint $table) {
            $table->String('reference_statu',15)->primary();
            $table->String('libelle_statu',50);
            $table->dateTime("created_at_statu")->default(date("Y-m-d H:i:s"));
            $table->dateTime("updated_at_statu")->default(date("Y-m-d H:i:s"));
        });
    }
    public function down(): void{
        Schema::dropIfExists('status');
    }
};
