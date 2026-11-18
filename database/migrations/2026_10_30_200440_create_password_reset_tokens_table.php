
<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // The password_reset_tokens table is already created
        // in the original users migration.
    }

    public function down(): void
    {
        // The original migration owns this table.
    }
};
