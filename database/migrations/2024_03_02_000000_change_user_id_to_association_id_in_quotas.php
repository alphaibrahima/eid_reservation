<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quotas', function (Blueprint $table) {
            // Supprimer l'ancienne colonne
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
    
            // Ajouter la nouvelle colonne
            $table->foreignId('association_id')
                  ->constrained('associations') // Référence explicite
                  ->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('quotas', function (Blueprint $table) {
            $table->dropForeign(['association_id']);
            $table->dropColumn('association_id');
            
            // Optionnel : recréer user_id si besoin de rollback
            $table->foreignId('user_id')->constrained();
        });
    }
};
