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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('mobile')->unique();
            $table->string('chinese_name')->nullable();
            $table->integer('status')->default(1);
            $table->integer('user_type_id')->default(3);
            $table->date('dob');
            $table->string('religion', 50)->nullable();
            $table->string('race')->nullable();
            $table->text('address_1')->nullable();
            $table->text('address_2')->nullable();
            $table->string('postcode')->nullable();
            $table->string('state', 50)->nullable();
            $table->string('country', 50);
            $table->string('referral')->nullable();
            $table->ipAddress('register_ip')->nullable()->default('::1');
            $table->ipAddress('last_login_ip')->nullable()->default('::1');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('lang', 10)->nullable();
            $table->decimal('cash_wallet', 11, 2)->default(0.00);
            $table->string('kyc_approval')->default('pending');
            $table->text('kyc_approval_description')->nullable();
            $table->string('gender')->nullable();
            $table->string('id_no')->nullable();
            $table->string('cash_wallet_id')->nullable();
            //$table->string('ib_id')->nullable();
            $table->string('ib_id')->nullable();
            $table->unsignedBigInteger('upline_id')->nullable();
            $table->string('hierarchyList')->nullable();
            $table->decimal('total_deposit', 11, 2)->default(0.00);
            $table->decimal('total_withdrawal', 11, 2)->default(0.00);
            $table->decimal('total_group_deposit', 11, 2)->default(0.00);
            $table->decimal('total_group_withdrawal', 11, 2)->default(0.00);
            $table->integer('direct_ib')->default(0);
            $table->integer('total_ib')->default(0);
            $table->integer('direct_client')->default(0);
            $table->integer('total_client')->default(0);
            $table->string('remark')->nullable();
            $table->integer('ct_user_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
