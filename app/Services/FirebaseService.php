<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\WebPushConfig;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification($token, $title, $body, $url, $sound)
    {
        $notification = Notification::create($title, $body);
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification)
            ->withWebPushConfig(WebPushConfig::fromArray([
                'notification' => [
                    'click_action' => $url,
                    'sound' => $sound,
                ],
            ]));
        try {
            $data = $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::info($e);
            return false;
        }
    }
}
