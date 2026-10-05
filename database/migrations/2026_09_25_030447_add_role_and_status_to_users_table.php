<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The initial users table migration already defines both columns.
    }

    public function down(): void
    {
        // This migration did not change the schema.
    }
};
