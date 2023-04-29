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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('school_type_id')->constrained('school_types');
            $table->foreignId('supervisor_id')->constrained('supervisors');
            $table->string('principal')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('api_token', 40)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
