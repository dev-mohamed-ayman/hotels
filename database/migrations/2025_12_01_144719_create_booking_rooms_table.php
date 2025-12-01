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
        Schema::create('booking_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('room_type', ['TPL', 'DBL', 'SGL', 'QUD']);
            $table->integer('room_count')->default(1);
            $table->string('category')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('margin', 10, 2)->default(0);
            $table->integer('child_count')->default(0);
            $table->decimal('child_margin', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_rooms');
    }
};
