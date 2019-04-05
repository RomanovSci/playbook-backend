<?php

namespace App\Services\SmsDelivery;

use App\Services\ExecResult;
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
    public function send(string $phone, string $text): ExecResult
    {
        try {
            $success = $this->mobizonApi->call('message', 'sendSMSMessage', [
                'recipient' => $phone,
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ExecResult::instance();
        }

        if ($success) {
            return ExecResult::instance()
                ->setSuccess()
                ->setData(
                    (array) $this->mobizonApi->getData()
                );
        }

        return ExecResult::instance();
    }
}
