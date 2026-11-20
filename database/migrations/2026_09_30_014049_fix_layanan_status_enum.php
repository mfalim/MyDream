```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE layanan
            MODIFY status ENUM(
                'Draft Vendor',
                'Aktif',
                'Selesai',
                'Nonaktif'
            )
            NOT NULL
            DEFAULT 'Draft Vendor'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE layanan
            MODIFY status ENUM(
                'Draft Vendor',
                'Aktif',
                'Selesai',
                'Nonaktif'
            )
            NOT NULL
            DEFAULT 'Draft Vendor'
        ");
    }
};