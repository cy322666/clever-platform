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
        Schema::create('getcourse_payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->integer('number')->nullable();
            $table->integer('order_id')->nullable();
            $table->string('positions')->nullable();
            $table->string('left_cost_money')->nullable();
            $table->string('cost_money')->nullable();
            $table->string('payed_money')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('link')->nullable();

            $table->integer('status')->nullable();

            $table->integer('webhook_id')->nullable();
            $table->integer('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('getcourse_payments');
    }
};
