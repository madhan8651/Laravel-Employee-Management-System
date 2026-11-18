<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_email_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('birthday_year');

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['employee_id', 'birthday_year'],
                'birthday_email_employee_year_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_email_logs');
    }
};
