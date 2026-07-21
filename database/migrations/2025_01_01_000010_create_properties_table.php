<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('property_type', ['apartment', 'house', 'commercial', 'land', 'studio', 'maisonette']);
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('TZS');
            $table->string('region');
            $table->string('district');
            $table->string('ward')->nullable();
            $table->string('street')->nullable();
            $table->text('exact_location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('area_sqm')->nullable();
            $table->boolean('is_furnished')->default(false);
            $table->boolean('is_available')->default(true);
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired', 'rented'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_sponsored')->default(false);
            $table->timestamp('featured_until')->nullable();
            $table->timestamp('sponsored_until')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('unlock_count')->default(0);
            $table->json('amenities')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
