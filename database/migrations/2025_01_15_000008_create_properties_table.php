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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wilaya_id')->constrained()->onDelete('cascade');
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_category_id')->constrained()->onDelete('cascade');

            // Multilingual fields
            $table->string('title_fr');
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('description_fr')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            // Location
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Pricing and capacity
            $table->decimal('price_per_night_dzd', 10, 2);
            $table->unsignedInteger('surface_area')->nullable();
            $table->unsignedInteger('rooms')->nullable();
            $table->unsignedInteger('bedrooms')->nullable();
            $table->unsignedInteger('bathrooms')->nullable();
            $table->unsignedInteger('max_guests')->default(1);

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->unsignedInteger('views_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['is_active', 'is_verified']);
            $table->index(['wilaya_id', 'commune_id']);
            $table->index('price_per_night_dzd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
