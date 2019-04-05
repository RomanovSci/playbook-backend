<?php

namespace App\Services\SmsDelivery;

use App\Services\ExecResult;
use Illuminate\Support\Facades\Log;
use Nexmo\Client;
use Nexmo\Client\Credentials\Basic;

/**
 * Class SmsDeliveryServiceNexmo
 * @package App\Services\SmsDeliveryService
 */
class SmsDeliveryServiceNexmo implements SmsDeliveryServiceInterface
{
    const STATUS_SUCCESS = '0';

    /**
     * @var Client
     */
    protected $apiClient;

    /**
     * SmsDeliveryServiceMobizon constructor.
     */
    public function __construct()
    {
        try {
            $this->apiClient = new Client(
                new Basic(
                    env('SMS_DELIVERY_NEXMO_API_KEY'),
                    env('SMS_DELIVERY_NEXMO_API_SECRET')
                )
            );
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     * @param string $phone
     * @param string $text
     * @return array
     * @throws \Exception
     */
    public function send(string $phone, string $text): ExecResult
    {
        try {
            $message = $this->apiClient->message()->send([
                'to' => $phone,
                'from' => 'PlayBook',
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ExecResult::instance()->setMessage($e->getMessage());
        }

        if ($message->getStatus() === self::STATUS_SUCCESS) {
            return ExecResult::instance()
                ->setSuccess()
                ->setData(
                    $message->getResponseData()['messages'][0]
                );
        }

        return ExecResult::instance();
    }
}
