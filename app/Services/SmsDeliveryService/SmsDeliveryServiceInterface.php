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
     * Return format:
     *  [
     *      'success' => true|false,
     *      'data' => [
     *          ...
     *      ]
     *  ]
     *
     * @param string $phone
     * @param string $text
     * @return array
     */
    public function send(string $phone, string $text): array;
}
