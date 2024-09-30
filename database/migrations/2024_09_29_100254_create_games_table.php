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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('link')->unique();
            $table->string('genres')->nullable();
            $table->date('release_date')->nullable();
            $table->string('publisher')->nullable();
            $table->string('reviews')->nullable();
            $table->string('rating')->nullable();
            $table->string('price')->nullable();
            $table->json('positions')->nullable();
            $table->json('history')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
