<?php

namespace App\Services\SmsDeliveryService;

use Illuminate\Support\Facades\Log;
use Mobizon\MobizonApi;

/**
 * Class SmsDeliveryServiceMobizon
 *
 * @package App\Services\SmsDeliveryService
 */
class SmsDeliveryServiceMobizon implements SmsDeliveryServiceInterface
{
    protected $mobizonApi;

    /**
     * SmsDeliveryServiceMobizon constructor.
     *
     * @throws \Mobizon\Mobizon_ApiKey_Required
     * @throws \Mobizon\Mobizon_Curl_Required
     * @throws \Mobizon\Mobizon_Error
     * @throws \Mobizon\Mobizon_OpenSSL_Required
     */
    public function __construct()
    {
        $this->mobizonApi = new MobizonApi([
            'apiKey' => env('SMS_DELIVERY_MOBIZON_KEY'),
            'apiServer' => env('SMS_DELIVERY_MOBIZON_DOMAIN')
        ]);
    }

    /**
     * @inheritdoc
     * @param string $phone
     * @param string $text
     * @return bool
     */
    public function send(string $phone, string $text): bool
    {
        try {
            $success = $this->mobizonApi->call('message', 'sendSMSMessage', [
                'recipient' => $phone,
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }

        if ($success) {
            $data = $this->mobizonApi->getData();
            // TODO: Save message data

            return true;
        }

        return false;
    }
}
