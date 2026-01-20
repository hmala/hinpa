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
        Schema::table('private_wing_services', function (Blueprint $table) {
            $table->decimal('insurance_percentage', 5, 2)->default(0)->after('is_insured')->comment('نسبة تحمل المريض عند الضمان');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_wing_services', function (Blueprint $table) {
            $table->dropColumn('insurance_percentage');
        });
    }
};
