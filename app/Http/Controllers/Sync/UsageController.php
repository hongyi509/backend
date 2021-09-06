<?php

namespace App\Http\Controllers\Sync;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UsageController extends Controller
{
    public function postStats(Request $request)
    {
        //Valider le request
        $data = $request->validate([
            'userUuid' => 'required|size:36',
            'appVersion' => 'nullable|max:15',
            'userAgent' => 'nullable|max:1000',
            'passportCount' => 'required|integer|min:0',
        ]);

        //Stocker la stat
        User::updateOrCreate([
            'uuid' => $data['userUuid'],
        ],[
            'last_seen_at' => Carbon::now(),
            'last_version' => $data['appVersion'],
            'last_user_agent' => $data['userAgent'],
            'passport_count' => $data['passportCount'],
        ]);

        return [
            'status' => 'ok',
        ];
    }
}