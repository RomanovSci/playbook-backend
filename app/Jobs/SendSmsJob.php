<?php
declare(strict_types = 1);

namespace App\Jobs;

use App\Models\SmsDelivery;
use App\Services\SmsDelivery\Providers\Twilio;
use App\Services\SmsDelivery\SmsDeliveryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

/**
 * Class SendSmsJob
 * @package App\Jobs
 */
class SendSmsJob implements ShouldQueue
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
     * @param SmsDeliveryInterface|Twilio $smsDeliveryService
     * @return void
     * @throws \Throwable
     */
    public function handle(SmsDeliveryInterface $smsDeliveryService): void
    {
        $data = [
            'phone' => $this->phone,
            'text' => $this->text,
            'success' => true,
        ];

        if (app()->environment() === 'production') {
            $result = $smsDeliveryService->send($this->phone, $this->text);
            $data['success'] = $result->getSuccess();
            $data['data'] = json_encode($result->getData());

            if (!$data['success'] && $result->getMessage()) {
                $data['data'] = json_encode([
                    'message' => $result->getMessage()
                ]);
            }
        }

        SmsDelivery::create($data);
    }
}
