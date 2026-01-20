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
            $table->text('cancellation_reason')->nullable()->after('is_insured')->comment('سبب الإبطال');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason')->comment('تاريخ الإبطال');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancelled_at')->comment('معرف المستخدم الذي أبطل');
            $table->foreign('cancelled_by')->references('id')->on('users');
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
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['cancellation_reason', 'cancelled_at', 'cancelled_by']);
        });
    }
};
