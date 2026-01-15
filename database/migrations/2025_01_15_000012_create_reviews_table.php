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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('rating')->min(1)->max(5);
            $table->text('comment_fr')->nullable();
            $table->text('comment_ar')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            // Ensure one review per reservation
            $table->unique('reservation_id');

            // Indexes
            $table->index(['property_id', 'is_visible']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
