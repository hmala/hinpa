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
        if (!Schema::hasTable('mohs')) {
            Schema::create('mohs', function (Blueprint $table) {
                $table->id();
                $table->integer('mohcode');
                $table->string('mohname');
                $table->timestamps();
                $table->softDeletes();
            });
        
    }
}
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mohs');
    }
};
