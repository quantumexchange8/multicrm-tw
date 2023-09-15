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
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('group');
            $table->double('minimum_deposit', 0, 2)->default(0);
            $table->integer('leverage')->default(0);
            $table->string('currency');
            $table->integer('allow_create_account');

            //rebate group
            $table->string('type');
            $table->string('commission_structure');
            $table->string('trade_open_duration');

            //effective date
            //
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            //other options
            $table->string('image')->nullable();

            $table->boolean('delete')->default(false);
            $table->boolean('show_register')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('group')
                ->references('id')
                ->on('groups')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_types');
    }
};
