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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->enum('vote', ['like', 'hate']);
            $table->timestamps();

            $table->unique(['user_id', 'movie_id']);
            $table->index(['movie_id', 'vote']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropForeign(['movie_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex(['movie_id', 'vote']);
        });
        Schema::dropIfExists('votes');
    }
};
