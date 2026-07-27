<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menghapus seluruh tabel stock opname.
     */
    public function up(): void
    {
        Schema::dropIfExists('stock_opnames');
    }

    /**
     * Mengembalikan tabel stock opname.
     */
    public function down(): void
    {
        Schema::create(
            'stock_opnames',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('item_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table
                    ->foreignId('user_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->integer('system_stock');
                $table->integer('physical_stock');
                $table->integer('difference');
                $table->date('opname_date');
                $table->text('note')->nullable();
                $table->timestamps();
            }
        );
    }
};
