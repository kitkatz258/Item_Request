<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM('Pending', 'Approved', 'Declined', 'Completed', 'Cancelled') DEFAULT 'Pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM('Pending', 'Approved', 'Rejected', 'Completed') DEFAULT 'Pending'");
        });
    }
};
