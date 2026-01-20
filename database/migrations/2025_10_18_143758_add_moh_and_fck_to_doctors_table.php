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
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('moh_id')->constrained('mohs')->onDelete('cascade');
            $table->foreignId('fck_id')->constrained('fcks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['moh_id']);
            $table->dropForeign(['fck_id']);
            $table->dropColumn(['moh_id', 'fck_id']);
        });
    }
};
