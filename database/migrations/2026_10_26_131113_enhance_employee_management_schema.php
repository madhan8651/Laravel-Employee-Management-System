
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * USERS
         * Add new columns as nullable initially so existing records survive.
         */
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique();
            $table->enum('role', ['admin', 'employee'])
                ->default('employee');
            $table->enum('status', ['active', 'inactive'])
                ->default('active');
        });

        // Backfill usernames for existing users.
        DB::table('users')->orderBy('id')->get(['id'])->each(
            function ($user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['username' => 'user-'.$user->id]);
            }
        );

        // Preserve admin access for the existing primary account (ID 1).
        DB::table('users')->where('id', 1)->update(['role' => 'admin']);

        /*
         * DEPARTMENTS
         */
        Schema::table('departments', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])
                ->default('active');
        });

        /*
         * EMPLOYEES
         */
        Schema::table('employees', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])
                ->nullable();
            $table->date('date_of_joining')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo')->nullable();

            $table->enum(
                'status',
                ['active', 'inactive', 'terminated']
            )->default('active');

            $table->softDeletes();

            $table->index(['designation', 'status']);
        });

        // Split existing employee names into first and last names.
        DB::table('employees')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->each(function ($employee) {
                $parts = preg_split(
                    '/\s+/',
                    trim($employee->name),
                    2
                );

                DB::table('employees')
                    ->where('id', $employee->id)
                    ->update([
                        'first_name' => $parts[0] ?? '',
                        'last_name' => $parts[1] ?? '',
                    ]);
            });

        // department_id already exists, so add its foreign key separately.
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
            $table->dropIndex(['designation', 'status']);

            $table->dropColumn([
                'first_name',
                'last_name',
                'user_id',
                'date_of_birth',
                'gender',
                'date_of_joining',
                'salary',
                'address',
                'profile_photo',
                'status',
                'deleted_at',
            ]);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'role', 'status']);
        });
    }
};
