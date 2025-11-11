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
        Schema::table('admin_todos', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_todos', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->index('user_id');
            }

            if (!Schema::hasColumn('admin_todos', 'title')) {
                $table->string('title')->default('')->after('user_id');
            }

            if (!Schema::hasColumn('admin_todos', 'note')) {
                $table->text('note')->nullable()->after('title');
            }

            if (!Schema::hasColumn('admin_todos', 'remind_at')) {
                $table->timestamp('remind_at')->nullable()->after('note');
                $table->index('remind_at');
            }

            if (!Schema::hasColumn('admin_todos', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('remind_at');
            }

            if (!Schema::hasColumn('admin_todos', 'is_completed')) {
                $table->boolean('is_completed')->default(false)->after('reminder_sent_at');
                $table->index('is_completed');
            }

            if (!Schema::hasColumn('admin_todos', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('is_completed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_todos', function (Blueprint $table) {
            $columns = [
                'completed_at',
                'is_completed',
                'reminder_sent_at',
                'remind_at',
                'note',
                'title',
                'user_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('admin_todos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};


