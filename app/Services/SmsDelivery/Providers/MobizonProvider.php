<?php

namespace App\Services\SmsDelivery\Providers;

use App\Services\ExecResult;
use App\Services\SmsDelivery\SmsDeliveryInterface;
use Illuminate\Support\Facades\Log;
use Mobizon\MobizonApi;

/**
 * Class MobizonProvider
 * @package App\Services\SmsDeliveryService
 */
class MobizonProvider implements SmsDeliveryInterface
{
    /**
     * @var MobizonApi
     */
    protected $mobizonApi;

    /**
     * Mobizon constructor.
     *
     * @return void
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
