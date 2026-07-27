<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menghapus nomor transaksi barang masuk dan keluar.
     */
    public function up(): void
    {
        Schema::table(
            'goods_receipts',
            function (Blueprint $table): void {
                $table->dropUnique(['receipt_number']);
                $table->dropColumn('receipt_number');
            }
        );

        Schema::table(
            'goods_issues',
            function (Blueprint $table): void {
                $table->dropUnique(['issue_number']);
                $table->dropColumn('issue_number');
            }
        );
    }

    /**
     * Mengembalikan nomor transaksi.
     */
    public function down(): void
    {
        Schema::table(
            'goods_receipts',
            function (Blueprint $table): void {
                $table
                    ->string('receipt_number')
                    ->nullable()
                    ->unique()
                    ->after('id');
            }
        );

        Schema::table(
            'goods_issues',
            function (Blueprint $table): void {
                $table
                    ->string('issue_number')
                    ->nullable()
                    ->unique()
                    ->after('id');
            }
        );
    }
};
