<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushNotificationController extends Controller
{
    protected $firebaseService;
    function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    public function save_fcm_token(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $user->fcm_token = $request->fcm_token;
        $user->save();
        return ["status" => 1];
    }

    public function send_notification(Request $request)
    {
        $user_list = User::get();
        foreach($user_list as $user)
        {
            if($user->fcm_token)
            {
                $deviceToken = $user->fcm_token;
                $title = "New Message From ".Auth::user()->name;
                $body = Auth::user()->name." Created New Notification";
                $url = "https://www.google.com/";
                $sound = "https://static.whatsapp.net/rsrc.php/yW/r/BS_BUUXbKq5.mp3";
                $this->firebaseService->sendNotification($deviceToken, $title, $body, $url, $sound);
            }
        }
    }
}
