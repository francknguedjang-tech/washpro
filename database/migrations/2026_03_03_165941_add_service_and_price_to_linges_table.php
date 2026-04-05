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
        Schema::table('linges', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
            $table->decimal('prix_unitaire', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('linges', function (Blueprint $table) {
            //
        });
    }
};
