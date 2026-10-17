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
        $table->string('role')->default('client'); // admin ou client
        $table->string('company_name')->nullable();
        $table->string('activity')->nullable();
        $table->string('phone')->nullable();
        $table->boolean('is_approved')->default(false); // l'admin valide le revendeur
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
