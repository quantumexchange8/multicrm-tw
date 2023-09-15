<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rebate_allocation_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requestBy');
            $table->unsignedBigInteger('handleBy')->nullable();
            $table->string('description')->nullable();
            $table->string('status')->default('pending');
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('requestBy')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('handleBy')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rebate_allocation_requests');
    }
};
