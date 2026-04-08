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
        Schema::table('paiements', function (Blueprint $table) {
            // On modifie l'énumération pour inclure 'g2tpay'
            $table->enum('mode_paiement', ['cache', 'orange_money', 'mobile_money', 'g2tpay'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->enum('mode_paiement', ['cache', 'orange_money', 'mobile_money'])->change();
        });
    }
};
