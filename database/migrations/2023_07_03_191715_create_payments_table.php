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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('from')->nullable();
            $table->unsignedInteger('to')->nullable();
            $table->string('user')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ticket')->nullable();
            $table->string('payment_id');
            $table->string('category'); //payment  internal transfer
            $table->string('type'); //deposit withdrawal wallettometa metatowallet metatometa
            $table->decimal('amount', 11, 2);
            $table->string('gateway')->nullable();
            $table->string('status')->default("Submitted");

            $table->string('channel')->nullable();
            $table->string('currency')->nullable();

            //crypto
            $table->string('TxID')->nullable();
            $table->string('receipt')->nullable();
            $table->text('description')->nullable();

            //withdraw
            $table->string('account_no')->nullable();
            $table->string('account_type')->nullable();

            //notes
            $table->date('approval_date')->nullable();
            $table->string('comment', 31)->nullable();
            $table->longText('reason')->nullable();
            $table->boolean('delete')->default(false);

            //
            $table->decimal('real_amount', 11, 2)->nullable();
            $table->string('payment_charges')->nullable();
            $table->unsignedBigInteger('handleBy')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('to')
                ->references('meta_login')
                ->on('trading_users')
                ->onUpdate('cascade');
            $table->foreign('from')
                ->references('meta_login')
                ->on('trading_users')
                ->onUpdate('cascade');
            $table->foreign('handleBy')
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
        Schema::dropIfExists('payments');
    }
};
