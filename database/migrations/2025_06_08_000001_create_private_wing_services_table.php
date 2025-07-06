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
        Schema::create('private_wing_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_wing_id')->constrained('private_wings')->onDelete('cascade');            $table->foreignId('service_id')->constrained('service_specialization');
            $table->decimal('service_fee', 10, 2); // التكلفة اليومية
            $table->boolean('is_daily')->default(false); // احتساب الأيام
            $table->decimal('total_amount', 10, 2); // الإجمالي
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
        Schema::dropIfExists('private_wing_services');
    }
};
