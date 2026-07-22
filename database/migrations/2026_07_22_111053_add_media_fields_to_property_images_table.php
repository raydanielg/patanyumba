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
        Schema::table('property_images', function (Blueprint $table) {
            $table->string('media_type')->default('image')->after('property_id');
            $table->string('video_url')->nullable()->after('image_path');
            $table->string('thumbnail_url')->nullable()->after('thumbnail_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_images', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'video_url', 'thumbnail_url']);
        });
    }
};
