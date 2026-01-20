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
        Schema::table('private_wings', function (Blueprint $table) {
            $table->string('patient_nationality')->nullable()->after('patient_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_wings', function (Blueprint $table) {
            $table->dropColumn('patient_nationality');
        });
    }
};
