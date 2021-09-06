<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Licence;
use App\Models\PaypalOrder;
use App\Models\PaypalOrderStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaypalOrderController extends Controller
{
    public function postInit(Request $request)
    {
        //Valider le request
        $data = $request->validate([
            'userUuid' => 'required|size:36',
            'email' => 'required|email|max:250',
            'product' => 'nullable|max:1000',
            'fullData' => 'required|json',
        ]);

        //Stocker le user
        $user = User::updateOrCreate([
            'uuid' => $data['userUuid'],
        ],[
            'last_email' => $data['email'],
        ]);

        //Valider le Json
        $json = json_decode($data['fullData'], true);
        Validator::make($json, [
            'orderID' => 'required',
        ]);

        //Stocker la order
        PaypalOrder::create([
            'user_id' => $user->id,
            'product' => $data['product'],
            'paypal_order' => $json['orderID'],
            'full_data' => $json,
        ]);

        return [
            'status' => 'ok',
        ];
    }

    public function postCaptured(Request $request)
    {
        //Valider le request
        $data = $request->validate([
            'userUuid' => 'required|size:36',
            'email' => 'required|email|max:250',
            'fullData' => 'required|json',
        ]);

        //Stocker le user
        $user = User::updateOrCreate([
            'uuid' => $data['userUuid'],
        ],[
            'last_email' => $data['email'],
        ]);

        //Valider le Json
        $json = json_decode($data['fullData'], true);
        Validator::make($json, [
            'id' => 'required',
            'intent' => 'required',
            'status' => 'required',
        ]);

        //Trouver la commande
        $order = PaypalOrder::where('paypal_order', $json['id'])->firstOrFail();

        //Stocker le order status
        PaypalOrderStatus::create([
            'paypal_order_id' => $order->id,
            'paypal_intent' => $json['intent'],
            'paypal_status' => $json['status'],
            'full_data' => $json,
        ]);

        //TODO plus tard déplacer ceci dans une job

        //Créer le client et la licence
        if ($json['intent'] === 'CAPTURE' && $json['status'] === 'COMPLETED') {
            $client = Client::firstOrCreate([
                'email' => $data['email'],
            ]);

            ClientUser::create([
                'client_id' => $client->id,
                'user_id' => $user->id,
                'source' => 'PURCHASE',
            ]);

            $licence = Licence::create([
                'product' => $order->product,
                'user_id' => $user->id,
                'client_id' => $client->id,
                'paypal_order_id' => $order->id,
            ]);

            return [
                'status' => 'ok',
                'data' => [
                    'product' => $order->product,
                    'licence' => $licence->uuid,
                ]
            ];
        }

        return [
            'status' => 'ok',
        ];
    }
}