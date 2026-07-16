<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menghapus dukungan soft delete dari tabel items.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Mengembalikan dukungan soft delete pada tabel items.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};
