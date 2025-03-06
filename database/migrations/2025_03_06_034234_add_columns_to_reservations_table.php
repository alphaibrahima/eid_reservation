<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->integer('quantity')->after('size');
            $table->date('date')->after('quantity');
            $table->string('payment_intent_id')->nullable()->after('date');
        });
    }
    
    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'date', 'payment_intent_id']);
        });
    }
};
