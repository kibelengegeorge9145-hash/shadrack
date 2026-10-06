<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['Tuition', 'Accommodation', 'Meals', 'Examination', 'Transport', 'Uniform', 'Laboratory', 'Computer', 'Sports', 'Other']);
            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('level', ['O-Level', 'A-Level'])->nullable();
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_structure_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('required_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->boolean('is_waived')->default(false);
            $table->timestamps();
            $table->unique(['student_id', 'fee_structure_id', 'academic_year_id', 'term_id'], 'student_fee_unique');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_fee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('fee_structure_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('method', ['cash', 'bank', 'mobile', 'cheque', 'card', 'other'])->default('cash');
            $table->string('reference')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('notes')->nullable();
            $table->enum('status', ['recorded', 'reversed', 'pending'])->default('recorded');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('student_fees');
        Schema::dropIfExists('fee_structures');
    }
};
