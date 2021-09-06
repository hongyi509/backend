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

class LicenceController extends Controller
{
    public function postExists(Request $request)
    {
        //Valider le request
        $data = $request->validate([
            'email' => 'required|email|max:250',
            'product' => 'nullable|max:1000',
        ]);

        //Trouver le user
        $client = Client::where('email', $data['email'])->first();

        if ($client) {
            //Trouver la licence
            $licence = Licence::where('client_id', $client->id)->where('product', $data['product'])->whereNotNull('paid_at')->first();

            if ($licence) {
                return [
                    'status' => 'ok',
                    'data' => [
                        'exists' => true,
                    ]
                ];
            }
        }

        return [
            'status' => 'ok',
            'data' => [
                'exists' => false,
            ]
        ];
    }

    public function postRestore(Request $request)
    {
        //Valider le request
        $data = $request->validate([
            'userUuid' => 'required|size:36',
            'email' => 'required|email|max:250',
            'key' => 'required|size:5',
        ]);

        //Stocker le user
        $user = User::updateOrCreate([
            'uuid' => $data['userUuid'],
        ],[
            'last_email' => $data['email'],
        ]);

        //Trouver le client
        $client = Client::where('email', $data['email'])->where('key', $data['key'])->first();

        if ($client) {
            ClientUser::create([
                'client_id' => $client->id,
                'user_id' => $user->id,
                'source' => 'RESTORE',
            ]);

            //Trouver la licence
            $licences = Licence::where('client_id', $client->id)->whereNotNull('paid_at')->get();

            return [
                'status' => 'ok',
                'data' => $licences->map(function ($licence) { return [ 'product' => $licence->product, 'licence' => $licence->uuid]; }),
            ];
        }

        return [
            'status' => 'ok',
            'data' => []
        ];
    }
}