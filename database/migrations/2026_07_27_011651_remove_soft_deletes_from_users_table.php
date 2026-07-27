<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menghapus akun lama yang masih berstatus soft deleted.
     */
    public function up(): void
    {
        /**
         * Menghapus permanen semua akun yang sebelumnya
         * sudah dihapus menggunakan soft delete.
         */
        DB::table('users')
            ->whereNotNull('deleted_at')
            ->delete();

        Schema::table(
            'users',
            function (Blueprint $table): void {
                $table->dropSoftDeletes();
            }
        );
    }

    /**
     * Mengembalikan kolom soft delete pengguna.
     */
    public function down(): void
    {
        Schema::table(
            'users',
            function (Blueprint $table): void {
                $table->softDeletes();
            }
        );
    }
};
