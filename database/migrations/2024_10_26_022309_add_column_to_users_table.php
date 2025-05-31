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
            $table->string('user_type')->after('email')->nullable()->default('user');
            $table->string('phone')->after('email')->nullable();
            $table->string('address')->after('email')->nullable();
            $table->date('dob')->after('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_type');
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('dob');
        });
    }
};
