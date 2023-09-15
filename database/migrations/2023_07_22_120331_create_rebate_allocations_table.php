<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rebate_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->unsignedBigInteger('from')->nullable();
            $table->unsignedBigInteger('to')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('request_id')
                ->references('id')
                ->on('rebate_allocation_requests')
                ->onUpdate('cascade');
            $table->foreign('from')
                ->references('id')
                ->on('ib_account_types')
                ->onUpdate('cascade');
            $table->foreign('to')
                ->references('id')
                ->on('ib_account_types')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rebate_allocations');
    }
};
