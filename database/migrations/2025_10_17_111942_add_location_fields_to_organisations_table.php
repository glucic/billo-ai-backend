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
        Schema::table('organisations', function (Blueprint $table) {
            $table->string('address')->nullable()->after('description');
            $table->string('street')->nullable()->after('address');
            $table->string('city')->nullable()->after('street');
            $table->string('zip')->nullable()->after('city');
            $table->string('region')->nullable()->after('zip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn(['street', 'city', 'zip', 'region']);
        });
    }
};
