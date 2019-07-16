<?php
declare(strict_types = 1);

namespace App\Jobs;

use App\Models\SmsDelivery;
use App\Services\SmsDelivery\SmsDeliveryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
     * @param SmsDeliveryInterface $smsDeliveryService
     * @return void
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
        }

        SmsDelivery::create($data);
    }
}
