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
        Schema::create('gateway_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gateway');
            $table->string('base_currency');
            $table->string('target_currency');
            $table->double('deposit_rate', 0, 2)->default(0);
            $table->double('withdrawal_rate', 0, 2)->default(0);
            $table->string('deposit_charge_type');
            $table->double('deposit_charge_amount', 0, 2)->default(0);
            $table->string('withdrawal_charge_type');
            $table->double('withdrawal_charge_amount', 0, 2)->default(0);
            $table->string('status')->default('Active');
            $table->string('delete')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('gateway')
                ->references('id')
                ->on('gateway_lists')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateway_exchange_rates');
    }
};
