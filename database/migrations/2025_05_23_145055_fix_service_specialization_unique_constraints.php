<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_specialization', function (Blueprint $table) {
            // إزالة القيد الفريد القديم على عمود codesv
            $table->dropUnique('service_specialization_codesv_unique');
            
            // إضافة قيد فريد جديد يجمع بين codesv و service_id و type_specialization_id
            $table->unique(['codesv', 'service_id', 'type_specialization_id'], 'unique_service_specialization_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_specialization', function (Blueprint $table) {
            // حذف القيد الفريد الجديد
            $table->dropUnique('unique_service_specialization_code');
            
            // إعادة القيد الفريد القديم
            $table->unique('codesv', 'service_specialization_codesv_unique');
        });
    }
};
