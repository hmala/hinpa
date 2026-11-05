<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('private_wings', function (Blueprint $table) {
            if (!Schema::hasColumn('private_wings', 'exception_from')) {
                $table->string('exception_from')->nullable()->after('is_insured');
            }
            if (!Schema::hasColumn('private_wings', 'exception_percentage')) {
                $table->decimal('exception_percentage', 5, 2)->default(0)->after('exception_from');
            }
            if (!Schema::hasColumn('private_wings', 'exception_attachment')) {
                $table->string('exception_attachment')->nullable()->after('exception_percentage');
            }
        });
    }

    public function down(): void
    {
        Schema::table('private_wings', function (Blueprint $table) {
            if (Schema::hasColumn('private_wings', 'exception_attachment')) {
                $table->dropColumn('exception_attachment');
            }
            if (Schema::hasColumn('private_wings', 'exception_percentage')) {
                $table->dropColumn('exception_percentage');
            }
            if (Schema::hasColumn('private_wings', 'exception_from')) {
                $table->dropColumn('exception_from');
            }
        });
    }
};
