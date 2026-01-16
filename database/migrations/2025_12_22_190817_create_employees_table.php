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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Personal Details
            $table->enum('employee_type', ['Permanent', 'Contract'])->default('Permanent');
            $table->string('full_name');
            $table->string('father_mother_name')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Transgender', 'Other'])->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            
            // Official Details
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->string('employment_status')->nullable();
            
            // Contract Details (Conditional)
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            
            // Documents
            $table->string('appointment_letter')->nullable();
            $table->string('id_proof')->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
