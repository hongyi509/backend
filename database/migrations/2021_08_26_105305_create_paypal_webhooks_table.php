<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaypalWebhooksTable extends Migration
{
    public function up()
    {
        Schema::create('paypal_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('paypal_webhook_id', 250)->index();
            $table->string('paypal_resource_type', 36);
            $table->string('paypal_resource_version', 36);
            $table->string('paypal_event_type', 36);
            $table->json('full_data');
            $table->timestamps();
            $table->timestamp('processed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paypal_webhooks');
    }

/*
{
"id": "WH-0144117147889625F-8602746681976005W",
"event_version": "1.0",
"create_time": "2021-08-18T14:58:33.448Z",
"resource_type": "checkout-order",
"resource_version": "2.0",
"event_type": "CHECKOUT.ORDER.APPROVED",
"summary": "An order has been approved by buyer",
"resource": {
"update_time": "2021-08-18T14:58:15Z",
"create_time": "2021-08-18T14:58:06Z",
"purchase_units": [
{
"reference_id": "default",
"amount": {
"currency_code": "CAD",
"value": "0.99"
},
"payee": {
"email_address": "sb-nixcl7194964@business.example.com",
"merchant_id": "MXZ4KU8KEH938",
"display_data": {
"brand_name": "QR Québec"
}
},
"description": "QR Québec - Famille",
"custom_id": "QRQC-FAMILY",
"soft_descriptor": "PAYPAL *TEST STORE",
"payments": {
"captures": [
{
"id": "6LS29360CW874860W",
"status": "COMPLETED",
"amount": {
"currency_code": "CAD",
"value": "0.99"
},
"final_capture": true,
"seller_protection": {
"status": "ELIGIBLE",
"dispute_categories": [
"ITEM_NOT_RECEIVED",
"UNAUTHORIZED_TRANSACTION"
]
},
"seller_receivable_breakdown": {
"gross_amount": {
"currency_code": "CAD",
"value": "0.99"
},
"paypal_fee": {
"currency_code": "CAD",
"value": "0.33"
},
"net_amount": {
"currency_code": "CAD",
"value": "0.66"
}
},
"links": [
{
"href": "https://api.sandbox.paypal.com/v2/payments/captures/6LS29360CW874860W",
"rel": "self",
"method": "GET"
},
{
"href": "https://api.sandbox.paypal.com/v2/payments/captures/6LS29360CW874860W/refund",
"rel": "refund",
"method": "POST"
},
{
"href": "https://api.sandbox.paypal.com/v2/checkout/orders/1UT86467RW744162R",
"rel": "up",
"method": "GET"
}
],
"create_time": "2021-08-18T14:58:15Z",
"update_time": "2021-08-18T14:58:15Z"
}
]
}
}
],
"links": [
{
"href": "https://api.sandbox.paypal.com/v2/checkout/orders/1UT86467RW744162R",
"rel": "self",
"method": "GET"
}
],
"id": "1UT86467RW744162R",
"intent": "CAPTURE",
"payer": {
"name": {
"given_name": "John",
"surname": "Doe"
},
"email_address": "sb-cqn2x7196707@personal.example.com",
"payer_id": "46EYHQWQZHSLL",
"address": {
"country_code": "CA"
}
},
"status": "COMPLETED"
},
"links": [
{
"href": "https://api.sandbox.paypal.com/v1/notifications/webhooks-events/WH-0144117147889625F-8602746681976005W",
"rel": "self",
"method": "GET"
},
{
"href": "https://api.sandbox.paypal.com/v1/notifications/webhooks-events/WH-0144117147889625F-8602746681976005W/resend",
"rel": "resend",
"method": "POST"
}
]
}




{
"id": "WH-75D78948UP093622P-3JX476490S061750H",
"event_version": "1.0",
"create_time": "2021-08-18T14:58:33.209Z",
"resource_type": "capture",
"resource_version": "2.0",
"event_type": "PAYMENT.CAPTURE.COMPLETED",
"summary": "Payment completed for CAD 0.99 CAD",
"resource": {
"amount": {
"value": "0.99",
"currency_code": "CAD"
},
"seller_protection": {
"dispute_categories": [
"ITEM_NOT_RECEIVED",
"UNAUTHORIZED_TRANSACTION"
],
"status": "ELIGIBLE"
},
"supplementary_data": {
"related_ids": {
"order_id": "1UT86467RW744162R"
}
},
"update_time": "2021-08-18T14:58:15Z",
"create_time": "2021-08-18T14:58:15Z",
"final_capture": true,
"seller_receivable_breakdown": {
"paypal_fee": {
"value": "0.33",
"currency_code": "CAD"
},
"gross_amount": {
"value": "0.99",
"currency_code": "CAD"
},
"net_amount": {
"value": "0.66",
"currency_code": "CAD"
}
},
"custom_id": "QRQC-FAMILY",
"links": [
{
"method": "GET",
"rel": "self",
"href": "https://api.sandbox.paypal.com/v2/payments/captures/6LS29360CW874860W"
},
{
"method": "POST",
"rel": "refund",
"href": "https://api.sandbox.paypal.com/v2/payments/captures/6LS29360CW874860W/refund"
},
{
"method": "GET",
"rel": "up",
"href": "https://api.sandbox.paypal.com/v2/checkout/orders/1UT86467RW744162R"
}
],
"id": "6LS29360CW874860W",
"status": "COMPLETED"
},
"links": [
{
"href": "https://api.sandbox.paypal.com/v1/notifications/webhooks-events/WH-75D78948UP093622P-3JX476490S061750H",
"rel": "self",
"method": "GET"
},
{
"href": "https://api.sandbox.paypal.com/v1/notifications/webhooks-events/WH-75D78948UP093622P-3JX476490S061750H/resend",
"rel": "resend",
"method": "POST"
}
]
}
*/
}
