<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $schema = config('database.connections.mysql.engine') ?? '';
        Schema::table('result_items', function (Blueprint $table) {
            if (DB::connection()->getDriverName() !== 'sqlite') {
                $table->unsignedSmallInteger('marks')
                    ->nullable()
                    ->change();

                DB::statement(
                    'ALTER TABLE result_items ADD CONSTRAINT result_items_marks_check CHECK (marks IS NULL OR (marks > 0 AND marks <= 100))'
                );
            }
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('result_items', function (Blueprint $table) {
            DB::statement('ALTER TABLE result_items DROP CONSTRAINT result_items_marks_check');
        });
    }
};
