<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('contact_phone')->nullable()->after('description');
            $table->enum('listing_type', ['single', 'multi_unit'])->default('single')->after('property_type');
            $table->integer('total_units')->default(1)->after('listing_type');
            $table->enum('rental_period', ['day', 'week', 'month', 'year'])->default('month')->after('currency');
            $table->decimal('price_min', 12, 2)->nullable()->after('price');
            $table->decimal('price_max', 12, 2)->nullable()->after('price_min');
        });

        Schema::create('property_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('unit_name');
            $table->string('unit_number')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('area_sqm')->nullable();
            $table->integer('floor_number')->nullable();
            $table->integer('max_occupants')->nullable();
            $table->boolean('is_furnished')->default(false);
            $table->boolean('is_available')->default(true);
            $table->json('amenities')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_units');
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['contact_phone', 'listing_type', 'total_units', 'rental_period', 'price_min', 'price_max']);
        });
    }
};
