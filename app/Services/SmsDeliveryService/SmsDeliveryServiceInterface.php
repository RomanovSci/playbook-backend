<?php

namespace App\Services\SmsDeliveryService;

/**
 * Interface SmsDeliveryServiceInterface
 * @package App\Services\SmsDeliveryService
 */
interface SmsDeliveryServiceInterface
{
    /**
     * Send sms message
     *
     * @param string $phone
     * @param string $text
     * @return bool
     */
    public function send(string $phone, string $text): bool;
}
