<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the existing unique index on email.
        Schema::table('employees', function ($table) {
            $table->dropUnique('employees_email_unique');
        });

        // Create a generated column containing the email only
        // for active (non-deleted) employees.
        DB::statement("
            ALTER TABLE employees
            ADD COLUMN active_email VARCHAR(255)
            GENERATED ALWAYS AS (
                CASE
                    WHEN deleted_at IS NULL THEN email
                    ELSE NULL
                END
            ) STORED
        ");

        // Enforce uniqueness among active employees.
        Schema::table('employees', function ($table) {
            $table->unique('active_email', 'employees_active_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function ($table) {
            $table->dropUnique('employees_active_email_unique');
        });

        Schema::table('employees', function ($table) {
            $table->dropColumn('active_email');
        });

        Schema::table('employees', function ($table) {
            $table->unique('email', 'employees_email_unique');
        });
    }
};
