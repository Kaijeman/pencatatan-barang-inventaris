<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menghapus kode barang.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }

    /**
     * Mengembalikan kode barang.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table
                ->string('code')
                ->nullable()
                ->unique()
                ->after('category_id');
        });
    }
};
