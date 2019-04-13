<?php

namespace App\Services\SmsDelivery;

use App\Jobs\SendSmsJob;
use App\Services\ExecResult;

/**
 * Class SmsDeliveryService
 * @package App\Services\SmsDelivery
 */
class SmsDeliveryService implements SmsDeliveryInterface
{
    /**
     * @inheritdoc
     * @param string $phone
     * @param string $text
     * @return ExecResult
     */
    public function send(string $phone, string $text): ExecResult
    {
        SendSmsJob::dispatch($phone, $text)->onConnection('redis');
        return ExecResult::instance()->setSuccess();
    }
}
