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
        Schema::create('school_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')/* ->constrained('schools') */;
            $table->string('year');
            $table->integer('math');
            $table->integer('ind_lit');
            $table->integer('eng_lit');
            $table->integer('science');
            $table->integer('social');
            $table->integer('civic');
            $table->integer('islam');
            $table->integer('catholic');
            $table->integer('protestant');
            $table->integer('hindu');
            $table->integer('buddha');
            $table->integer('konghucu');
            $table->integer('counseling');
            $table->integer('sports');
            $table->integer('art');
            $table->integer('entrepreneurship');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_teachers');
    }
};
