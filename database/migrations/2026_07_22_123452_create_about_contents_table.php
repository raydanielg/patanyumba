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
        Schema::create('about_contents', function (Blueprint $table) {
            $table->id();
            $table->string('section')->default('main'); // main, mission, vision, values, team, contact, stats
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('icon')->nullable();
            $table->string('image_url')->nullable();
            $table->json('stats')->nullable(); // for stats section: [{label, value, icon}]
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_contents');
    }
};
