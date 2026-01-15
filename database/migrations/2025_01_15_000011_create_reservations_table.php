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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_user_id')->constrained('users')->onDelete('cascade');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedInteger('guests_count')->default(1);
            $table->decimal('total_price_dzd', 10, 2);
            $table->string('currency_code', 3)->default('DZD');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['property_id', 'check_in_date', 'check_out_date']);
            $table->index(['client_user_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
