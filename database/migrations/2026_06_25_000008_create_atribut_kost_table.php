<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('atribut_kost', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kost_id')->constrained('kost')->onDelete('cascade');
            $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
            $table->foreignId('opsi_kriteria_id')->constrained('opsi_kriteria')->onDelete('cascade');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('atribut_kost');
    }
};
