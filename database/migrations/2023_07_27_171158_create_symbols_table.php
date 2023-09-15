<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('symbols', function (Blueprint $table) {
            $table->id();
            $table->string('display')->nullable();
            $table->string('value')->nullable();
            $table->string('symbol_group');
            $table->string('meta_symbol_name');
            $table->string('meta_path');
            $table->string('meta_digits');
            $table->string('meta_contract_size');
            $table->decimal('meta_swap_long');
            $table->decimal('meta_swap_short');
            $table->string('meta_swap_3_day');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symbols');
    }
};
