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
        Schema::create('surgeries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fck_id');
            $table->foreign('fck_id')->references('id')->on('fcks');
            $table->unsignedBigInteger('moh_id');
            $table->unsignedBigInteger('fctypesid');
            $table->foreign('fctypesid')->references('id')->on('fctypes');
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
           
            $table->unsignedBigInteger('surgtyp');
            $table->foreign('surgtyp')->references('id')->on('surgs');
            $table->integer('khasa');
            $table->integer('fkubra');
            $table->integer('kubra');
            $table->integer('mtws');
            $table->integer('sugra');
           
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
        Schema::dropIfExists('surgeries');
    }
};
