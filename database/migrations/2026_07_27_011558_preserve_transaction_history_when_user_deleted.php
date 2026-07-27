<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan nama pencatat dan mengizinkan pengguna dihapus.
     */
    public function up(): void
    {
        Schema::table(
            'goods_receipts',
            function (Blueprint $table): void {
                $table->string(
                    'recorded_by_name',
                    100
                )
                    ->nullable()
                    ->after('user_id');
            }
        );

        Schema::table(
            'goods_issues',
            function (Blueprint $table): void {
                $table->string(
                    'recorded_by_name',
                    100
                )
                    ->nullable()
                    ->after('user_id');
            }
        );

        /**
         * Mengisi nama pencatat pada transaksi lama.
         */
        DB::statement(
            '
            UPDATE goods_receipts AS receipt
            INNER JOIN users AS user
                ON user.id = receipt.user_id
            SET receipt.recorded_by_name = user.name
            WHERE receipt.recorded_by_name IS NULL
            '
        );

        DB::statement(
            '
            UPDATE goods_issues AS goods_issue
            INNER JOIN users AS user
                ON user.id = goods_issue.user_id
            SET goods_issue.recorded_by_name = user.name
            WHERE goods_issue.recorded_by_name IS NULL
            '
        );

        /**
         * Mengubah foreign key barang masuk.
         */
        Schema::table(
            'goods_receipts',
            function (Blueprint $table): void {
                $table->dropForeign(['user_id']);
            }
        );

        Schema::table(
            'goods_receipts',
            function (Blueprint $table): void {
                $table->unsignedBigInteger('user_id')
                    ->nullable()
                    ->change();
            }
        );

        Schema::table(
            'goods_receipts',
            function (Blueprint $table): void {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        );

        /**
         * Mengubah foreign key barang keluar.
         */
        Schema::table(
            'goods_issues',
            function (Blueprint $table): void {
                $table->dropForeign(['user_id']);
            }
        );

        Schema::table(
            'goods_issues',
            function (Blueprint $table): void {
                $table->unsignedBigInteger('user_id')
                    ->nullable()
                    ->change();
            }
        );

        Schema::table(
            'goods_issues',
            function (Blueprint $table): void {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        );
    }

    /**
     * Menghapus penyimpanan nama pencatat.
     */
    public function down(): void
    {
        Schema::table(
            'goods_receipts',
            function (Blueprint $table): void {
                $table->dropForeign(['user_id']);

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->restrictOnDelete();

                $table->dropColumn('recorded_by_name');
            }
        );

        Schema::table(
            'goods_issues',
            function (Blueprint $table): void {
                $table->dropForeign(['user_id']);

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->restrictOnDelete();

                $table->dropColumn('recorded_by_name');
            }
        );
    }
};
