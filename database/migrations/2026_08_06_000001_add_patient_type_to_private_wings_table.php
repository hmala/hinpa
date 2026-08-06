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
        Schema::table('private_wings', function (Blueprint $table) {
            $table->enum('patient_type', ['inpatient', 'outpatient'])->default('inpatient')->after('statistical_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_wings', function (Blueprint $table) {
            $table->dropColumn('patient_type');
        });
    }
};
