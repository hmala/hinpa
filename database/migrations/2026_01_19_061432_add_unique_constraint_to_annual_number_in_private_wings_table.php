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
        // استخدام SQL مباشر لإضافة unique constraint مع تحديد طول حقل hospital
        DB::statement('ALTER TABLE private_wings ADD UNIQUE unique_annual_per_hospital (hospital(100), annual_number, fiscal_year)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // حذف unique constraint باستخدام SQL مباشر
        DB::statement('ALTER TABLE private_wings DROP INDEX unique_annual_per_hospital');
    }
};
