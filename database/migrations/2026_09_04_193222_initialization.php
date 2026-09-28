<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Initializes the database on fresh installations by importing the
     * baseline SQL schema and provisioning the default administrator account.
     */
    public function up(): void
    {
        // Check if baseline tables already exist (using 'withdrawals' as the sentinel table).
        // If missing, this indicates a fresh installation that requires schema initialization.
        if (!Schema::hasTable('withdrawals')) {
            // Locate the baseline database SQL dump
            $sqlFile = public_path('install/database.sql');
            if (!File::exists($sqlFile)) {
                $sqlFile = base_path('public/install/database.sql');
            }

            // Import and execute the raw schema dump
            if (File::exists($sqlFile)) {
                DB::unprepared(File::get($sqlFile));
            }

            // Provision the default administrator account if not already created
            if (Schema::hasTable('admins') && !DB::table('admins')->where('email', 'admin@admin.com')->exists()) {
                DB::table('admins')->insert([
                    'name' => 'Admin',
                    'username' => 'admin',
                    'email' => 'admin@admin.com',
                    'password' => Hash::make('password'),
                    'status' => 1,
                    'lang' => 'en',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * No rollback: The baseline initialization migration should not
     * drop tables or purge data to prevent catastrophic data loss.
     */
    public function down(): void
    {
        // No rollback: Initial baseline schema is retained.
    }
};
