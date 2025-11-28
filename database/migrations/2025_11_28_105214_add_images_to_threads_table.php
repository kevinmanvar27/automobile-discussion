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
        Schema::table('threads', function (Blueprint $table) {
            // Remove the old single image column if it exists
            if (Schema::hasColumn('threads', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
        
        // Create a separate table for thread images
        Schema::create('thread_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('thread_id');
            $table->string('image_path');
            $table->timestamps();
            
            $table->foreign('thread_id')->references('id')->on('threads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thread_images');
        
        Schema::table('threads', function (Blueprint $table) {
            // Recreate the old single image column if needed
            if (!Schema::hasColumn('threads', 'image_path')) {
                $table->string('image_path')->nullable();
            }
        });
    }
};