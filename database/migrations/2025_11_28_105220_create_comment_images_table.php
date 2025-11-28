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
        // Drop the old single image column from comments table
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
        
        // Create a separate table for comment images
        Schema::create('comment_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->string('image_path');
            $table->timestamps();
            
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_images');
        
        // Recreate the old single image column in comments table
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'image_path')) {
                $table->string('image_path')->nullable();
            }
        });
    }
};