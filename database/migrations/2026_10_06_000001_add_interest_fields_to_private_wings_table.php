<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('private_wings', function (Blueprint $table) {
            if (!Schema::hasColumn('private_wings', 'interest_rate')) {
                $table->decimal('interest_rate', 5, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('private_wings', 'interest_amount')) {
                $table->decimal('interest_amount', 12, 2)->default(0)->after('interest_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('private_wings', function (Blueprint $table) {
            if (Schema::hasColumn('private_wings', 'interest_amount')) {
                $table->dropColumn('interest_amount');
            }
            if (Schema::hasColumn('private_wings', 'interest_rate')) {
                $table->dropColumn('interest_rate');
            }
        });
    }
};

