<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_reservations')->default(50);
            $table->boolean('available')->default(true);
            
            // // Clé étrangère optimisée
            // $table->foreignId('association_id')
            //     ->constrained('users')
            //     ->onDelete('cascade');

            $table->timestamps();
            
            // Index pour performances
            $table->index(['date', 'available']);
            // $table->index(['association_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('slots');
    }
};