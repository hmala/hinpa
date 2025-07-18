<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        Schema::create('services', function (Blueprint $table) {            $table->id();
            $table->integer('sercode')->unique();
            $table->string('sername');
         $table->unsignedBigInteger('type_specializations_id');
            $table->foreign('type_specializations_id')->references('id')->on('type_specializations');    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
};
