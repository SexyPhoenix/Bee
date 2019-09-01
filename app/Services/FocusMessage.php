<?php
namespace App\Services;


use App\Messages\Message;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FocusMessage
{

    /**
     * version rain - for OA
     * @param $userId
     * @param $link
     * @param $message
     * @param string $app
     * @param int $isMessage
     * @param int $identity
     */
    public static function send($userId, $link, $message, $app='attendance', $isMessage = 1, $identity = 0)
    {
        //测试通知
        info( 'send focus message success');
    }

    public static function done($userId, $app, $identity)
    {
        return true;
    }
}