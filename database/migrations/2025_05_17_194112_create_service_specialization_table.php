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
            $table->string('namesv');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services');
            $table->unsignedBigInteger('type_specializations_id');
            $table->foreign('type_specializations_id')->references('id')->on('type_specializations');  
                      $table->timestamps();
            
            // Ensure unique combination of service and specialization
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