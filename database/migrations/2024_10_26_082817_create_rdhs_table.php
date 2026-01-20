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
        Schema::create('rdhs', function (Blueprint $table) {
            $table->id();
            $table->integer('Spcuno');
            $table->string('Spcuname')->nullable();
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
        Schema::dropIfExists('rdhs');
    }
};
