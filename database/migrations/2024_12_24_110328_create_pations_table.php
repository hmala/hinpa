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
        Schema::create('pations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fck_id');
            $table->foreign('fck_id')->references('id')->on('fcks');
            $table->unsignedBigInteger('moh_id');
            $table->unsignedBigInteger('fctypesid');
            $table->foreign('fctypesid')->references('id')->on('fctypes');
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->string('spehosp')->nullable();
            $table->unsignedBigInteger('spebed');
            $table->foreign('spebed')->references('id')->on('rdhs');
            $table->integer('unitnum')->nullable();
            $table->integer('bedm');
            $table->integer('outpationmon');
            $table->integer('stayoutpation');
            $table->integer('mkoth');
            $table->integer('death');
            $table->string('creator');
            $table->boolean('save');
            $table->timestamps();
            $table->string('Created_by');
            $table->softDeletes();
            $table->date('Approv_dt')->nullable();
            $table->integer('status_value');
            $table->string('status');
            $table->string('hfcode');
            $table->integer('f_approve');
            $table->string('note');
            $table->string('Approv_user');
            $table->string('fapprove');

            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pations');
    }
};
