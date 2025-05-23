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
        Schema::table('service_specialization', function (Blueprint $table) {
            $table->dropUnique('service_specialization_codesv_unique');
            $table->dropUnique('service_specialization_service_id_type_specialization_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_specialization', function (Blueprint $table) {
            $table->unique('codesv');
            $table->unique(['service_id', 'type_specialization_id']);
        });
    }
};
