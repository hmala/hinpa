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
        Schema::create('Fctypes', function (Blueprint $table) {
            $table->id();
            $table->string('Fname', 999);
            $table->unsignedBigInteger('moh_id');
            $table->foreign('moh_id')->references('id')->on('mohs')->onDelete('cascade');
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
        Schema::dropIfExists('fctypes');
    }
};