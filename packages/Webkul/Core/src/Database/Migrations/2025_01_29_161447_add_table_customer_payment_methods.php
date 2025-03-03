<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_payment_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_payment_method_type');
            $table->unsignedInteger('customer_id')->nullable()->comment('null if guest checkout');
            $table->unsignedInteger('cart_id')->nullable()->comment('only for cart_payments');
            $table->unsignedInteger('order_id')->nullable()->comment('only for order_payments');
            $table->string('company_name')->nullable();
            $table->string('account_holder_name');
            $table->string('account_holder_email');
            $table->string('account_type');
            $table->string('account_no')->nullable();
            $table->string('account_no_last_four_digit', 4);
            $table->string('account_expired');
            $table->boolean('is_account_verfied');
            $table->boolean('default_customer_payment_method')->default(false)->comment('only for customer_payment_methods');
            $table->json('additional')->nullable();
            $table->timestamps();

            $table->foreign(['customer_id'])->references('id')->on('customers')->onDelete('cascade');
            $table->foreign(['cart_id'])->references('id')->on('cart')->onDelete('cascade');
            $table->foreign(['order_id'])->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_payment_methods');
    }
};
