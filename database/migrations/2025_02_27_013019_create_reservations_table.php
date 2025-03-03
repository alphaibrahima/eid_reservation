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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Acheteur
            $table->foreignId('slot_id')->constrained(); // Créneau
            $table->foreignId('association_id')->constrained('users');            
            $table->enum('size', ['grand', 'moyen', 'petit']);
            $table->string('code')->unique();
            $table->enum('status', ['pending', 'confirmed', 'canceled'])->default('confirmed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
