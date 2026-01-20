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
        Schema::create('private_wings', function (Blueprint $table) {
            $table->id();
            $table->string('hospital')->nullable(); // مستشفى
            $table->string('health_department')->nullable(); // دائرة صحة
            $table->string('patient_name')->nullable(); // اسم المريض
            $table->string('file_number')->nullable(); // رقم ملفة المريض
            $table->string('statistical_number')->nullable(); // الرقم الإحصائي
            $table->date('entry_date')->nullable(); // تاريخ الدخول
            $table->date('exit_date')->nullable(); // تاريخ الخروج
            $table->integer('days_count')->nullable(); // عدد الأيام
            
            // الإيرادات
            $table->decimal('patient_bed_fee', 10, 2)->nullable(); // أجور رقود المريض
            $table->decimal('companion_bed_fee', 10, 2)->nullable(); // أجور رقود المرافق
            $table->decimal('nutrition_fee', 10, 2)->nullable(); // أجور التغذية
            $table->decimal('medicine_supplies_fee', 10, 2)->nullable(); // أجور الأدوية والمستلزمات
            $table->decimal('laboratory_tests_fee', 10, 2)->nullable(); // أجور الفحوص المختبرية
            $table->decimal('xray_fees', 10, 2)->nullable(); // أجور الفحوصات الشعاعية
            $table->decimal('sonar_fees', 10, 2)->nullable(); // أجور فحوصات السونار
            
            // معلومات الدفع
            $table->decimal('deposit_amount', 10, 2)->nullable(); // مبلغ التأمينات
            $table->string('receipt_number')->nullable(); // رقم وتاريخ الوصل
            $table->date('receipt_date')->nullable(); // تاريخ الوصل
            
            $table->decimal('total_amount', 10, 2)->nullable(); // المبلغ المتبقي / ردیات الأمانات
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('private_wings');
    }
};
