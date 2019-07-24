<?php
declare(strict_type = 1);

namespace App\Services\SmsDelivery\Providers;

use App\Services\ExecResult;
use App\Services\SmsDelivery\SmsDeliveryInterface;

/**
 * Class Twilio
 * @package App\Services\SmsDelivery\Providers
 */
class Twilio implements SmsDeliveryInterface
{
    /**
     * @param string $phone
     * @param string $text
     * @return ExecResult
     */
    public function send(string $phone, string $text): ExecResult
    {
        try {
            $twilio = new \Aloha\Twilio\Twilio(
                config('sms.twilio.sid'),
                config('sms.twilio.token'),
                config('sms.twilio.sender')
            );
            $sendResult = $twilio->message($phone, $text);

            return ExecResult::instance()->setSuccess()->setData((array) $sendResult);
        } catch (\Throwable $e) {
            return ExecResult::instance()->setMessage($e->getMessage());
        }
    }
}
