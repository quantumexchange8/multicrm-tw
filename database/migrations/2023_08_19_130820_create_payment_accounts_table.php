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
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('payment_account_name')->nullable();
            $table->string('payment_platform');
            $table->string('payment_platform_name');
            $table->string('account_no');
            $table->text('bank_branch_address')->nullable();
            $table->string('bank_swift_code')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('bank_code_type')->nullable();
            $table->string('country')->nullable();
            $table->string('currency', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_accounts');
    }
};
