<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('organisation_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('member'); // Possible values: admin, member
            $table->timestamps();
            
            // Prevent duplicate relationships
            $table->unique(['user_id', 'organisation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisation_user');
    }
};
