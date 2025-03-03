<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Changer la référence vers la table users
            $table->foreignId('association_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['association_id']);
            $table->dropColumn('association_id');
        });
    }
};