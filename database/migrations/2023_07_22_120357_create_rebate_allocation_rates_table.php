<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rebate_allocation_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('allocation_id');
            $table->unsignedBigInteger('symbol_group');
            $table->double('old', 0, 2);
            $table->double('new', 0, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('allocation_id')
                ->references('id')
                ->on('rebate_allocations')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rebate_allocation_rates');
    }
};
