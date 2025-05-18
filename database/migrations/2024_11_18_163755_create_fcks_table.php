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
        Schema::create('fcks', function (Blueprint $table) {
            $table->id();
            $table->string('Fckname' )->nullable();
            $table->unsignedBigInteger('moh_id');
            $table->foreign('moh_id')->references('id')->on('mohs');
            $table->unsignedBigInteger('fctypesid');
            $table->foreign('fctypesid')->references('id')->on('fctypes');
           
            $table->decimal('longitude')->nullable();
            $table->decimal('latitude')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->string('Created_by');
            $table->string('hfcode' )->nullable();
            $table->string('fcbelong' )->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fcks');
    }
};
