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
        Schema::create('ib_account_type_symbol_group_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ib_account_type');
            $table->unsignedBigInteger('symbol_group');
            $table->double('amount', 0, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('ib_account_type')
                ->references('id')
                ->on('ib_account_types')
                ->onUpdate('cascade');
            $table->foreign('symbol_group')
                ->references('id')
                ->on('symbol_groups')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ib_account_type_symbol_group_rates');
    }
};
