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
        Schema::create('account_type_symbol_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_type');
            $table->unsignedBigInteger('symbol_group');
            $table->double('amount', 0, 2)->default(0);
            $table->foreign('account_type')
                ->references('id')
                ->on('account_types')
                ->onUpdate('cascade');
            $table->foreign('symbol_group')
                ->references('id')
                ->on('symbol_groups')
                ->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_type_symbol_groups');
    }
};
