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
        Schema::table('tada_claims', function (Blueprint $table) {
            $table->json('bill_files')->nullable()->after('bill_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tada_claims', function (Blueprint $table) {
            $table->dropColumn('bill_files');
        });
    }
};
