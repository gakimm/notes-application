<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('visibility', ['private', 'shared', 'public'])->default('private');
            $table->string('slug')->unique()->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'visibility']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};