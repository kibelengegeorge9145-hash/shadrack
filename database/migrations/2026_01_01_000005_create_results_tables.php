<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['O-Level', 'A-Level']);
            $table->unsignedSmallInteger('min_mark');
            $table->unsignedSmallInteger('max_mark');
            $table->string('grade', 5);
            $table->unsignedTinyInteger('points')->default(0);
            $table->boolean('is_pass')->default(true);
            $table->timestamps();
        });

        Schema::create('division_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['O-Level', 'A-Level']);
            $table->string('division', 20);
            $table->unsignedTinyInteger('min_points');
            $table->unsignedTinyInteger('max_points');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->enum('level', ['O-Level', 'A-Level']);
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'published'])->default('draft');
            $table->unsignedInteger('total_marks')->default(0);
            $table->decimal('average', 6, 2)->default(0);
            $table->unsignedInteger('total_points')->default(0);
            $table->string('division')->nullable();
            $table->string('position')->nullable();
            $table->unsignedInteger('passed_subjects')->default(0);
            $table->unsignedInteger('failed_subjects')->default(0);
            $table->text('teacher_comment')->nullable();
            $table->text('parent_comment')->nullable();
            $table->text('head_comment')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'academic_year_id', 'term_id', 'school_class_id'], 'results_unique');
        });

        Schema::create('result_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('marks')->nullable();
            $table->string('grade', 5)->nullable();
            $table->unsignedTinyInteger('points')->default(0);
            $table->text('teacher_comment')->nullable();
            $table->timestamps();
            $table->unique(['result_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_items');
        Schema::dropIfExists('results');
        Schema::dropIfExists('division_rules');
        Schema::dropIfExists('grading_rules');
    }
};
