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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('bank_account_number')->nullable()->after('address');
            $table->string('bank_name')->nullable()->after('bank_account_number');
            $table->string('ifsc_code')->nullable()->after('bank_name');
            $table->string('pan_card_number')->nullable()->after('ifsc_code');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null')->after('department');
            $table->foreignId('designation_id')->nullable()->constrained('designations')->onDelete('set null')->after('designation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['designation_id']);
            $table->dropColumn(['bank_account_number', 'bank_name', 'ifsc_code', 'pan_card_number', 'department_id', 'designation_id']);
        });
    }
};

