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
        Schema::table('users', function (Blueprint $table) {
            $table->string('shop_name');
            $table->string('mobile_no');
            $table->string('city');
            $table->text('address');
            $table->boolean('verified')->default(false);
            $table->string('generated_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['shop_name', 'mobile_no', 'city', 'address', 'verified', 'generated_password']);
        });
    }
};
