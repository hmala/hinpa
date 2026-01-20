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
            $table->boolean('is_insured')->default(false)->after('is_daily')->comment('مشمول بالضمان');
            $table->decimal('patient_amount', 10, 2)->default(0)->after('is_insured')->comment('المبلغ المدفوع من المريض');
            $table->decimal('insurance_amount', 10, 2)->default(0)->after('patient_amount')->comment('المبلغ المستحق من الضمان');
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
            $table->dropColumn(['is_insured', 'patient_amount', 'insurance_amount']);
        });
    }
};
