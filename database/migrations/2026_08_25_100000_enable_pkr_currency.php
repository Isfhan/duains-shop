<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // 1) Enable the already-seeded PKR currency
        DB::table('mshop_locale_currency')
            ->where('id', 'PKR')
            ->update(['status' => 1, 'mtime' => $now]);

        // 2) Make PKR the default currency for the site locale
        DB::table('mshop_locale')
            ->where('currencyid', 'USD')
            ->update(['currencyid' => 'PKR', 'mtime' => $now]);
    }

    public function down(): void
    {
        $now = now();

        DB::table('mshop_locale')
            ->where('currencyid', 'PKR')
            ->update(['currencyid' => 'USD', 'mtime' => $now]);

        DB::table('mshop_locale_currency')
            ->where('id', 'PKR')
            ->update(['status' => 0, 'mtime' => $now]);
    }
};
