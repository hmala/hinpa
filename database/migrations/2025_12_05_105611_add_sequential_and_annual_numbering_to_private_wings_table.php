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
            $table->string('sequential_number')->nullable()->after('file_number')->comment('الترقيم التسلسلي حسب المؤسسة');
            $table->string('annual_number')->nullable()->after('sequential_number')->comment('الترقيم السنوي حسب المؤسسة');
            $table->year('fiscal_year')->nullable()->after('annual_number')->comment('السنة المالية');
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
            $table->dropColumn(['sequential_number', 'annual_number', 'fiscal_year']);
        });
    }
};
