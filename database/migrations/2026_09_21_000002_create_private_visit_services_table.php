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
        Schema::create('private_visit_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('private_visit_id');
            $table->unsignedBigInteger('service_id');
            $table->decimal('service_fee', 10, 2);
            $table->decimal('patient_amount', 10, 2);
            $table->decimal('insurance_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('operation_degree')->nullable();
            $table->timestamps();

            $table->foreign('private_visit_id')->references('id')->on('private_visits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_visit_services');
    }
};
