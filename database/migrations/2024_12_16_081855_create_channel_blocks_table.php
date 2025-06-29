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
        Schema::create('channel_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('channel_id');
            $table->unsignedBigInteger('blocker_id');
            $table->timestamps();

            $table->foreign('blocker_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_blocks');
    }
};
