<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaypalOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('paypal_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('paypal_order', 250)->unique();
            $table->string('product', 36)->index();
            $table->json('full_data');
            $table->timestamps();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('paypal_orders');
    }

/*
{
   "orderID":"44595098J32302600",
   "payerID":"46EYHQWQZHSLL",
   "paymentID":null,
   "billingToken":null,
   "facilitatorAccessToken":"A21AALSN0SpghYmR_iI1aTngXQjvDd18RR2EVWAEwR0pi8OsFWFk7iD6qaLayWPSVLQeYaCIHnRAgok69Wy7YwC8hj2sP233A"
}
*/
}
