<?php

namespace App\Services\SmsDeliveryService;

use Illuminate\Support\Facades\Log;
use Mobizon\MobizonApi;

/**
 * Class SmsDeliveryServiceMobizon
 * @package App\Services\SmsDeliveryService
 */
class SmsDeliveryServiceMobizon implements SmsDeliveryServiceInterface
{
    /**
     * @var MobizonApi
     */
    protected $mobizonApi;

    /**
     * SmsDeliveryServiceMobizon constructor.
     */
    public function __construct()
    {
        try {
            $this->mobizonApi = new MobizonApi([
                'apiKey' => env('SMS_DELIVERY_MOBIZON_KEY'),
                'apiServer' => env('SMS_DELIVERY_MOBIZON_DOMAIN')
            ]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     * @param string $phone
     * @param string $text
     * @return array
     */
    public function send(string $phone, string $text): array
    {
        try {
            $success = $this->mobizonApi->call('message', 'sendSMSMessage', [
                'recipient' => $phone,
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [
                'success' => false,
                'data' => null,
            ];
        }

        if ($success) {
            return [
                'success' => true,
                'data' => $this->mobizonApi->getData()
            ];
        }

        return [
            'success' => false,
            'data' => null,
        ];
    }
}
