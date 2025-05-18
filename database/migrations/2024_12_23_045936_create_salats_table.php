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
        Schema::create('salats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fck_id');
            $table->foreign('fck_id')->references('id')->on('fcks');
            $table->unsignedBigInteger('moh_id');
            $table->foreign('moh_id')->references('id')->on('mohs');
            $table->unsignedBigInteger('fctypesid');
            $table->foreign('fctypesid')->references('id')->on('fctypes');
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();     
            $table->unsignedBigInteger('salid');
            $table->foreign('salid')->references('id')->on('salsurs');
            $table->integer('salnum');
            $table->integer('bsalnum');
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
        Schema::dropIfExists('salats');
    }
};
