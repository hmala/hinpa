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
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fck_id');
            $table->foreign('fck_id')->references('id')->on('fcks');
            $table->unsignedBigInteger('moh_id');
            $table->foreign('moh_id')->references('id')->on('mohs');
            $table->unsignedBigInteger('fctypesid');
            $table->foreign('fctypesid')->references('id')->on('fctypes');
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();     
           
            $table->integer('caseest');
            $table->integer('casekhaf');
            $table->integer('caseesemer');
            $table->integer('cheoutpationsan');
            $table->integer('cheoutpationlab');
            $table->integer('cheinpationsan');
            $table->integer('cheinpationlab');
            $table->integer('bedssav');
            $table->integer('otherbed');
            $table->integer('bedest');
            $table->integer('tbedsmoh');
            $table->integer('bedrdhclose');  
            $table->integer('totalbed');  
            $table->integer('tbedsf');  
            $table->boolean('creator');
            $table->boolean('save');
            $table->timestamps();
            $table->string('Created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cases');
    }
};
