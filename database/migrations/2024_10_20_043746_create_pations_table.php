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
            $table->string('Recno', 20);
            $table->string('Untno', 20);
            $table->string('Doh')->nullable();
            $table->integer('Spcuno')->nullable();
            $table->integer('Facility_type');
            $table->string('Hospital')->nullable();
            $table->string('City')->nullable();
            $table->string('Kda')->nullable();
            $table->string('Nha')->nullable();
            $table->string('Gender')->nullable();
            $table->string('Edu')->nullable();
            $table->string('Job')->nullable();
            $table->string('Status')->nullable();
            $table->date('B_date');
            $table->string('F_City')->nullable();
            $table->string('F_Kda')->nullable();
            $table->string('F_Nha')->nullable();
            $table->enum('Place', ['حضر', 'ريف'])->nullable();
            $table->string('Nation')->nullable();
            $table->date('Date_in');
            $table->date('Date_out');
            $table->string('Diag')->nullable();
            $table->string('Srg_type')->nullable();
            $table->string('Degree1')->nullable();
            $table->string('P_s')->nullable();
            $table->boolean('IsDisabled');
            $table->boolean('disty');
            $table->string('Res1')->nullable();
            $table->string('Res2')->nullable();
            $table->string('Res3')->nullable();
            $table->string('Res4')->nullable();
            $table->integer('Totalstay');
            $table->boolean('Smoking');
            $table->boolean('Sugr');
            $table->decimal('Weight', 8, 2);
            $table->boolean('Hyper');
            $table->string('P_Name')->nullable();
            $table->string('Mother_Name')->nullable();
            $table->string('Doctor_Name')->nullable();
            $table->string('Doctor_Cer')->nullable();
            $table->string('Doctor_Spe')->nullable();
            $table->string('PImg1')->nullable();
            $table->string('PImg2')->nullable();
            $table->string('PImg3')->nullable();
            $table->string('Send1')->nullable();
            $table->boolean('Blood1');
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
        Schema::dropIfExists('pations');
    }
};
