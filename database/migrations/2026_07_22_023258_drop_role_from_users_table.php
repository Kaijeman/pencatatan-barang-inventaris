<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menghapus pembagian role pengguna.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }

    /**
     * Mengembalikan pembagian role pengguna.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table
                ->enum('role', [
                    'kepala_gudang',
                    'staff_gudang',
                ])
                ->default('staff_gudang')
                ->after('password');
        });
    }
};
