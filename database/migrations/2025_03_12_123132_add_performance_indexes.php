<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/YYYY_MM_DD_HHMMSS_add_performance_indexes.php

public function up()
{
    Schema::table('slots', function (Blueprint $table) {
        $table->index(['date', 'available_capacity']);
        $table->index('association_id');
    });

    Schema::table('reservations', function (Blueprint $table) {
        $table->index(['user_id', 'status']);
        $table->index('code');
    });
}

public function down()
{
    Schema::table('slots', function (Blueprint $table) {
        $table->dropIndex(['date', 'available_capacity']);
        $table->dropIndex(['association_id']);
    });

    Schema::table('reservations', function (Blueprint $table) {
        $table->dropIndex(['user_id', 'status']);
        $table->dropIndex(['code']);
    });
}

};
