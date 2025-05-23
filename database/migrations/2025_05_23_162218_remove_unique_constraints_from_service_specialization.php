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
            // حذف جميع القيود الفريدة الموجودة
            $table->dropUnique(['codesv']);
            $table->dropUnique(['service_id', 'type_specialization_id']);

            // إضافة المؤشر المركب الفريد الجديد
            $table->unique(['codesv', 'service_id', 'type_specialization_id'], 'service_specialization_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_specialization', function (Blueprint $table) {
            // حذف المؤشر المركب
            $table->dropUnique('service_specialization_unique');

            // إعادة القيود القديمة
            $table->unique(['codesv']);
            $table->unique(['service_id', 'type_specialization_id']);
        });
    }
};
