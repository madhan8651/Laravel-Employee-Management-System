<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Archive legacy personal information from users.
     */
    public function up(): void
    {
        Schema::create('legacy_user_profile_data', function (Blueprint $table) {
            $table->id();

            // Intentionally no foreign key:
            // archived data must survive user account deletion.
            $table->unsignedBigInteger('user_id')->unique();

            $table->string('name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo')->nullable();

            $table->timestamp('archived_at');
        });

        $users = DB::table('users')
            ->orderBy('id')
            ->get([
                'id',
                'name',
                'phone',
                'address',
                'profile_photo',
            ]);

        if ($users->isNotEmpty()) {
            $archiveRows = $users->map(function ($user) {
                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'profile_photo' => $user->profile_photo,
                    'archived_at' => now(),
                ];
            })->all();

            DB::table('legacy_user_profile_data')->insert($archiveRows);
        }
    }

    /**
     * Remove the archive table when rolling back.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_user_profile_data');
    }
};
