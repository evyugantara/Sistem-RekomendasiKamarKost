<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['select', 'checkbox'])->default('select');
            $table->enum('category', ['umum', 'pribadi', 'bersama'])->default('umum');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};
