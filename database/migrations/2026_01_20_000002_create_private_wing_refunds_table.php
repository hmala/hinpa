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
        Schema::create('private_wing_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_wing_id')->constrained('private_wings')->onDelete('cascade');
            $table->decimal('amount', 15, 2)->comment('مبلغ الاسترداد');
            $table->string('receipt_number')->comment('رقم وصل الاسترداد');
            $table->date('refund_date')->comment('تاريخ الاسترداد');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->string('receiver_name')->comment('اسم المستلم');
            $table->string('receiver_id_number')->nullable()->comment('رقم هوية المستلم');
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
        Schema::dropIfExists('private_wing_refunds');
    }
};
