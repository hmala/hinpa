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
            // حذف القيد الفريد من عمود codesv
            $table->dropUnique('service_specialization_codesv_unique');
            // إضافة مؤشر عادي على codesv
            $table->index('codesv');
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
            // إعادة القيد الفريد
            $table->unique('codesv', 'service_specialization_codesv_unique');
            // حذف المؤشر العادي
            $table->dropIndex(['codesv']);
        });
    }
};
