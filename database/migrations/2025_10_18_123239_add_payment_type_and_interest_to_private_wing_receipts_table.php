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
        Schema::table('private_wing_receipts', function (Blueprint $table) {
            $table->enum('payment_type', ['كاش', 'إلكتروني'])->after('receipt_date')->default('كاش');
            $table->decimal('interest_rate', 5, 2)->after('payment_type')->default(0);
            $table->decimal('interest_amount', 10, 2)->after('interest_rate')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_wing_receipts', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'interest_rate', 'interest_amount']);
        });
    }
};
