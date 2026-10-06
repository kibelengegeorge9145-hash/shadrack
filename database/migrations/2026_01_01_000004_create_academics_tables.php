<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_combinations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('level', ['O-Level', 'A-Level']);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('subject_combination_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_combination_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->unique(['subject_combination_id', 'subject_id'], 'combo_subj_unique');
        });

        Schema::create('student_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'subject_id', 'academic_year_id'], 'std_subj_year_unique');
        });

        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['teacher_id', 'subject_id', 'school_class_id'], 'tch_subj_class_unique');
        });

        Schema::create('teacher_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->unique(['teacher_id', 'school_class_id'], 'tch_class_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_classes');
        Schema::dropIfExists('teacher_subjects');
        Schema::dropIfExists('student_subjects');
        Schema::dropIfExists('subject_combination_subject');
        Schema::dropIfExists('subject_combinations');
    }
};
