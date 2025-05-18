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
        Schema::create('hwadths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fck_id');
            $table->foreign('fck_id')->references('id')->on('fcks');
            $table->unsignedBigInteger('moh_id');
            $table->unsignedBigInteger('fctypesid');
            $table->foreign('fctypesid')->references('id')->on('fctypes');
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();           
            
            $table->integer('livebnm');
            $table->integer('livebnf');
            $table->integer('livebnkh');
            $table->integer('livebnt');

            $table->integer('livebsm');
            $table->integer('livebsf');
            $table->integer('livebskh');
            $table->integer('livebst');

            $table->integer('totalbm');
            $table->integer('totalbf');
            $table->integer('totalbkh');
            $table->integer('totalbt');

            $table->integer('bdeadnm');
            $table->integer('bdeadnf');
            $table->integer('bdeadnkh');
            $table->integer('bdeadnt');

            $table->integer('bdeadsm');
            $table->integer('bdeadsf');
            $table->integer('bdeadskh');
            $table->integer('bdeadst');

            $table->integer('deadlm');
            $table->integer('deadlf');
            $table->integer('deadlkh');
            $table->integer('deadlt');

            $table->integer('deadmm');
            $table->integer('deadmf');
            $table->integer('deadmkh');
            $table->integer('deadmt');

            $table->integer('totaldm');
            $table->integer('totaldf');
            $table->integer('totaldkh');
            $table->integer('totaldt');
        
            $table->integer('mdeadm');  
            $table->integer('mdeadf');
            $table->integer('mdeadkh');
            $table->integer('mdeadt');    
            $table->integer('deadtb');    
                      
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
        Schema::dropIfExists('hwadths');
    }
};
