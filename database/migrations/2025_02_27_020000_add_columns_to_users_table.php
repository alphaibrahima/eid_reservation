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
        Schema::table('users', function (Blueprint $table) {
            //
            // $table->string('role')->default('buyer');
            // $table->string('phone')->unique();
            // $table->boolean('phone_verified')->default(false);
            // $table->foreignId('association_id')->nullable()->constrained('associations');

            if (Schema::hasTable('associations')) {
                $table->unsignedBigInteger('association_id')->nullable();
                $table->foreign('association_id')->references('id')->on('associations')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
