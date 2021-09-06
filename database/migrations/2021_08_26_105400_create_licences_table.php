<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicencesTable extends Migration
{
    public function up()
    {
        Schema::create('licences', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->string('product', 36)->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('client_id')->index();
            $table->unsignedBigInteger('paypal_order_id')->index();
            $table->timestamps();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('paypal_order_id')->references('id')->on('paypal_orders');
        });
    }

    public function down()
    {
        Schema::dropIfExists('licences');
    }
}
