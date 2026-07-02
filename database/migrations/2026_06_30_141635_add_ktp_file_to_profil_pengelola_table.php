<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('profil_pengelola', function (Blueprint $table) {
            $table->string('ktp_file')->nullable()->after('ktp_number');
        });

        
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'active'");
        }
    }

    
    public function down(): void
    {
        Schema::table('profil_pengelola', function (Blueprint $table) {
            $table->dropColumn('ktp_file');
        });

        
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'");
        }
    }
};
