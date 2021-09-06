<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Mail\ClientKey;
use App\Models\Client;
use App\Models\Licence;
use App\Models\PaypalWebhook;
use App\Models\PaypalOrder;
use App\Models\PaypalOrderStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\VerifyWebhookSignature;
use PayPal\Api\WebhookEvent;

class PaypalWebhookController extends Controller
{
    private function getApiContext()
    {
        // Suppress DateTime warnings, if not set already
        date_default_timezone_set(@date_default_timezone_get());

        /**
         * All default curl options are stored in the array inside the PayPalHttpConfig class. To make changes to those settings
         * for your specific environments, feel free to add them using the code shown below
         * Uncomment below line to override any default curl options.
         */
        // \PayPal\Core\PayPalHttpConfig::$defaultCurlOptions[CURLOPT_SSLVERSION] = CURL_SSLVERSION_TLSv1_2;

        // #### SDK configuration
        // Register the sdk_config.ini file in current directory
        // as the configuration source.
        /*
        if(!defined("PP_CONFIG_PATH")) {
            define("PP_CONFIG_PATH", __DIR__);
        }
        */

        // ### Api context
        // Use an ApiContext object to authenticate
        // API calls. The clientId and clientSecret for the
        // OAuthTokenCredential class can be retrieved from
        // developer.paypal.com

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('services.paypal.client_id'),
                config('services.paypal.client_secret')
            )
        );

        // Comment this line out and uncomment the PP_CONFIG_PATH
        // 'define' block if you want to use static file
        // based configuration

        $apiContext->setConfig(
            array(
                'mode' => config('services.paypal.mode'),
                'log.LogEnabled' => false,
                //'log.FileName' => '../PayPal.log',
                //'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => false,
                //'cache.FileName' => '/PaypalCache' // for determining paypal cache directory
                //'http.CURLOPT_CONNECTTIMEOUT' => 30
                //'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );

        // Partner Attribution Id
        // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
        // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
        // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');

        return $apiContext;
    }

    public function postWebhook(Request $request)
    {
        //Valider le webhook via Paypal
        $requestBody = file_get_contents('php://input');

        if(!$requestBody) {
            return response()->json([
                'status' => 'failed',
                'error' => 'Missing request body',
            ], 500);
        }

        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_UPPER);

        if(!array_key_exists('PAYPAL-AUTH-ALGO', $headers)
            || !array_key_exists('PAYPAL-TRANSMISSION-ID', $headers)
            || !array_key_exists('PAYPAL-CERT-URL', $headers)
            || !array_key_exists('PAYPAL-TRANSMISSION-SIG', $headers)
            || !array_key_exists('PAYPAL-TRANSMISSION-TIME', $headers)
        ) {
            return response()->json([
                'status' => 'failed',
                'error' => 'Missing request headers',
            ], 500);
        }

        $signatureVerification = new VerifyWebhookSignature();
        $signatureVerification->setAuthAlgo($headers['PAYPAL-AUTH-ALGO']);
        $signatureVerification->setTransmissionId($headers['PAYPAL-TRANSMISSION-ID']);
        $signatureVerification->setCertUrl($headers['PAYPAL-CERT-URL']);
        $signatureVerification->setWebhookId(config('services.paypal.webhook_id'));
        $signatureVerification->setTransmissionSig($headers['PAYPAL-TRANSMISSION-SIG']);
        $signatureVerification->setTransmissionTime($headers['PAYPAL-TRANSMISSION-TIME']);
        $signatureVerification->setRequestBody($requestBody);
        $verificationRequest = clone $signatureVerification;

        try {
            $apiContext = $this->getApiContext();
            $output = $signatureVerification->post($apiContext);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ], 500);
        }

        if ($output->getVerificationStatus() !== 'SUCCESS')
        return response()->json([
            'status' => 'failed',
            'error' => 'Verification failed: ' . $output->getVerificationStatus(),
        ], 500);

        //Data
        $data = $request->all();

        //Valider le Json
        Validator::make($data, [
            'resource_type' => 'required',
            'resource_version' => 'required',
            'event_type' => 'required',
        ]);

        //Stocker le webhook
        $webhook = PaypalWebhook::create([
            'paypal_webhook_id' => $data['id'],
            'paypal_resource_type' => $data['resource_type'],
            'paypal_resource_version' => $data['resource_version'],
            'paypal_event_type' => $data['event_type'],
            'full_data' => $data,
        ]);

        //TODO scinder ci-bas dans une job

        //Selon le type de webhook
        if ($data['resource_type'] === 'checkout-order') {
            //Re-Valider le Json
            Validator::make($data, [
                'resource' => 'required',
                'resource.id' => 'required',
                'resource.status' => 'required',
            ]);

            //Trouver la commande
            $order = PaypalOrder::where('paypal_order', Arr::get($data, 'resource.id'))->first();

            if ($order) {
                //Stocker le order status
                PaypalOrderStatus::create([
                    'paypal_order_id' => $order->id,
                    'paypal_intent' => $data['event_type'],
                    'paypal_status' => Arr::get($data, 'resource.status'),
                    'paypal_webhook_id' => $webhook->id,
                    'full_data' => $data,
                ]);
            }
        }

        if ($data['resource_type'] === 'capture') {
            //Re-Valider le Json
            Validator::make($data, [
                'resource' => 'required',
                'resource.supplementary_data' => 'required',
                'resource.supplementary_data.related_ids' => 'required',
                'resource.supplementary_data.related_ids.order_id' => 'required',
                'resource.status' => 'required',
            ]);

            //Trouver la commande
            $order = PaypalOrder::where('paypal_order', Arr::get($data, 'resource.supplementary_data.related_ids.order_id'))->first();

            if ($order) {
                //Marquer comme payée
                $order->paid_at = Carbon::now();
                $order->save();

                //Stocker le order status
                PaypalOrderStatus::create([
                    'paypal_order_id' => $order->id,
                    'paypal_intent' => $data['event_type'],
                    'paypal_status' => Arr::get($data, 'resource.status'),
                    'paypal_webhook_id' => $webhook->id,
                    'full_data' => $data,
                ]);

                //Trouver la licence
                $licence = Licence::where('paypal_order_id', $order->id)->first();

                if ($licence) {
                    $licence->paid_at = Carbon::now();
                    $licence->save();

                    $client = Client::find($licence->client_id);

                    //Envoyer un courriel avec email + ID
                    if ($client) {
                        Mail::to($client)->bcc('dev@kyber.studio')->send(new ClientKey($client));
                    }
                }
            }
        }

        return [
            'status' => 'ok',
        ];
    }
}