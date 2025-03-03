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
            if (!Schema::hasColumn('users', 'contact_phone')) {
                $table->string('contact_phone')->after('email');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->after('contact_phone');
            }
            if (!Schema::hasColumn('users', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('address');
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
