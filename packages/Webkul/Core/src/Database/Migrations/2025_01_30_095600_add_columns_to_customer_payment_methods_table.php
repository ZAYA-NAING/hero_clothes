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
        Schema::table('customer_payment_methods', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->nullable()->after('customer_payment_method_type');
            $table->boolean('use_for_shipping')->default(0)->after('default_customer_payment_method');

            $table->foreign('parent_id')->references('id')->on('customer_payment_methods')->onDelete('set null');
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
