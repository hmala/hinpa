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
        Schema::table('private_wings', function (Blueprint $table) {
            $table->decimal('refund_amount', 15, 2)->default(0)->after('deposit_amount')->comment('المبلغ المسترد');
            $table->enum('refund_status', ['none', 'partial', 'completed'])->default('none')->after('refund_amount')->comment('حالة الاسترداد');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('private_wings', function (Blueprint $table) {
            $table->dropColumn(['refund_amount', 'refund_status']);
        });
    }
};
