<?php

namespace App\Jobs;

use App\Models\SmsDelivery;
use App\Services\SmsDeliveryService\SmsDeliveryServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class SendSms
 * @package App\Jobs
 */
class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $text;

    /**
     * Create a new job instance.
     *
     * @param string $text
     * @param string $phone
     * @return void
     */
    public function __construct(string $phone, string $text)
    {
        $this->phone = $phone;
        $this->text = $text;
    }

    /**
     * Execute the job.
     *
     * @param SmsDeliveryServiceInterface $smsDeliveryService
     * @return void
     */
    public function handle(SmsDeliveryServiceInterface $smsDeliveryService)
    {
        if (app()->environment() === 'production') {
            $result = $smsDeliveryService->send($this->phone, $this->text);
            SmsDelivery::create([
                'phone' => $this->phone,
                'text' => $this->text,
                'success' => $result['success'],
                'data' => $result['data'],
            ]);
        }
    }
}
