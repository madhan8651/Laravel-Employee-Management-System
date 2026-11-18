<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Encrypt existing employee addresses.
     */
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('employees')
                ->whereNotNull('address')
                ->where('address', '<>', '')
                ->orderBy('id')
                ->chunkById(100, function ($employees) {
                    foreach ($employees as $employee) {
                        DB::table('employees')
                            ->where('id', $employee->id)
                            ->update([
                                'address' => Crypt::encryptString(
                                    $employee->address
                                ),
                            ]);
                    }
                });
        });
    }

    /**
     * Restore plaintext addresses.
     *
     * Requires the original APP_KEY.
     */
    public function down(): void
    {
        DB::transaction(function () {
            DB::table('employees')
                ->whereNotNull('address')
                ->where('address', '<>', '')
                ->orderBy('id')
                ->chunkById(100, function ($employees) {
                    foreach ($employees as $employee) {
                        DB::table('employees')
                            ->where('id', $employee->id)
                            ->update([
                                'address' => Crypt::decryptString(
                                    $employee->address
                                ),
                            ]);
                    }
                });
        });
    }
};
