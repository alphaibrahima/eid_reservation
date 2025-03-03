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
        //
        Schema::table('users', function (Blueprint $table) {
            // Rendre toutes les colonnes optionnelles
            $table->string('phone')->nullable()->change();
            $table->string('contact_phone')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('registration_number')->nullable()->change();
            
            // Définir une valeur par défaut pour le rôle
            $table->string('role')->default('buyer')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
