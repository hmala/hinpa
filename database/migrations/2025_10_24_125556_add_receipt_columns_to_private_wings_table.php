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
            $table->string('receipt_number')->nullable()->after('deposit_amount'); // رقم وتاريخ الوصل
            $table->date('receipt_date')->nullable()->after('receipt_number'); // تاريخ الوصل
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
            $table->dropColumn(['receipt_number', 'receipt_date']);
        });
    }
};
