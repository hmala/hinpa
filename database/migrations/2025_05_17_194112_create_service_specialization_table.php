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
        Schema::create('service_specialization', function (Blueprint $table) {
            $table->id();
            
            $table->string('codesv')->unique()->comment('رمز الخدمة في التخصص');
            $table->string('namesv')->comment('اسم الخدمة في التخصص');
            $table->decimal('price', 10, 2)->default(0.00)->comment('السعر');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('type_specialization_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination of service and specialization
            $table->unique(['service_id', 'type_specialization_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_specialization');
    }
};