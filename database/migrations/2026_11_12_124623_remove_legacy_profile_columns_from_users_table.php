<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove personal profile fields from the users table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'phone',
                'address',
                'profile_photo',
            ]);
        });
    }

    /**
     * Restore the columns and recover their archived values.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo')->nullable();
        });

        if (Schema::hasTable('legacy_user_profile_data')) {
            $archivedUsers = DB::table('legacy_user_profile_data')->get();

            foreach ($archivedUsers as $archivedUser) {
                DB::table('users')
                    ->where('id', $archivedUser->user_id)
                    ->update([
                        'name' => $archivedUser->name,
                        'phone' => $archivedUser->phone,
                        'address' => $archivedUser->address,
                        'profile_photo' => $archivedUser->profile_photo,
                    ]);
            }
        }
    }
};
