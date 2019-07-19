<?php
declare(strict_type = 1);

namespace App\Services\SmsDelivery\Providers;

use App\Services\ExecResult;
use App\Services\SmsDelivery\SmsDeliveryInterface;
use Illuminate\Support\Facades\Log;
use Nexmo\Client;
use Nexmo\Client\Credentials\Basic;

/**
 * Class Nexmo
 * @package App\Services\SmsDeliveryService
 */
class NexmoProvider implements SmsDeliveryInterface
{
    public const STATUS_SUCCESS = '0';

    /**
     * @var Client
     */
    protected $apiClient;

    /**
     * Nexmo constructor.
     *
     * @return void
     */
    public function __construct()
    {
        try {
            $this->apiClient = new Client(
                new Basic(
                    config('sms.nexmo.api_key'),
                    config('sms.nexmo.api_secret')
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
