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
        Schema::create('private_visits', function (Blueprint $table) {
            $table->id();
            $table->string('hospital');
            $table->string('health_department');
            $table->unsignedBigInteger('moh_id');
            $table->unsignedBigInteger('fck_id');
            $table->string('visitor_name');
            $table->string('visitor_phone')->nullable();
            $table->string('visitor_nationality')->nullable();
            $table->string('annual_number');
            $table->string('sequential_number');
            $table->string('fiscal_year');
            $table->date('visit_date');
            $table->time('visit_time')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->enum('payment_type', ['كاش', 'إلكتروني'])->default('كاش');
            $table->string('receipt_number')->nullable();
            $table->date('receipt_date')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_visits');
    }
};
