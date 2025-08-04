<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('note_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained()->onDelete('cascade');
            $table->foreignId('shared_with_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('permission', ['read', 'comment'])->default('read');
            $table->timestamps();
            
            // Unique constraint untuk prevent duplicate shares
            $table->unique(['note_id', 'shared_with_user_id']);
            
            // Indexes
            $table->index('shared_with_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('note_shares');
    }
};