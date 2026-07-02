<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('kamar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kost_id')->constrained('kost')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->enum('status', ['tersedia', 'terisi'])->default('tersedia');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};
