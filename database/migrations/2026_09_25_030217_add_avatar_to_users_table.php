<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The users table and the earlier avatar migration already define this column.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration did not change the schema.
    }
};
