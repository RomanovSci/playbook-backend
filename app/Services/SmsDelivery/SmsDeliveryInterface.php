<?php

namespace App\Services\SmsDelivery;

use App\Services\ExecResult;

/**
 * Interface SmsDeliveryInterface
 * @package App\Services\SmsDeliveryService
 */
interface SmsDeliveryInterface
{
    /**
     * Send sms message
     *
     * @param string $phone
     * @param string $text
     * @return ExecResult
     */
    public function send(string $phone, string $text): ExecResult;
}
