<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaypalOrderStatusesTable extends Migration
{
    public function up()
    {
        Schema::create('paypal_order_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paypal_order_id')->index();
            $table->string('paypal_intent', 36);
            $table->string('paypal_status', 36);
            $table->unsignedBigInteger('paypal_webhook_id')->nullable();
            $table->json('full_data');
            $table->timestamps();

            $table->foreign('paypal_order_id')->references('id')->on('paypal_orders');
            $table->foreign('paypal_webhook_id')->references('id')->on('paypal_webhooks');
        });
    }

    public function down()
    {
        Schema::dropIfExists('paypal_order_statuses');
    }

/*
{
   "id":"44595098J32302600",
   "intent":"CAPTURE",
   "status":"COMPLETED",
   "purchase_units":[
      {
         "reference_id":"default",
         "amount":{
            "currency_code":"CAD",
            "value":"0.99"
         },
         "payee":{
            "email_address":"sb-nixcl7194964@business.example.com",
            "merchant_id":"MXZ4KU8KEH938"
         },
         "description":"QR Québec - Famille",
         "custom_id":"QRQC-FAMILY",
         "soft_descriptor":"PAYPAL *TEST STORE",
         "payments":{
            "captures":[
               {
                  "id":"5N359071C5294650C",
                  "status":"COMPLETED",
                  "amount":{
                     "currency_code":"CAD",
                     "value":"0.99"
                  },
                  "final_capture":true,
                  "seller_protection":{
                     "status":"ELIGIBLE",
                     "dispute_categories":[
                        "ITEM_NOT_RECEIVED",
                        "UNAUTHORIZED_TRANSACTION"
                     ]
                  },
                  "create_time":"2021-08-17T23:17:47Z",
                  "update_time":"2021-08-17T23:17:47Z"
               }
            ]
         }
      }
   ],
   "payer":{
      "name":{
         "given_name":"John",
         "surname":"Doe"
      },
      "email_address":"sb-cqn2x7196707@personal.example.com",
      "payer_id":"46EYHQWQZHSLL",
      "address":{
         "country_code":"CA"
      }
   },
   "create_time":"2021-08-17T23:17:39Z",
   "update_time":"2021-08-17T23:17:47Z",
   "links":[
      {
         "href":"https://api.sandbox.paypal.com/v2/checkout/orders/44595098J32302600",
         "rel":"self",
         "method":"GET"
      }
   ]
}
*/
}
