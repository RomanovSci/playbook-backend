<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twilio provider
    |--------------------------------------------------------------------------
    */
    'twilio' => [
        'sid' => env('SMS_DELIVERY_TWILIO_SID'),
        'token' => env('SMS_DELIVERY_TWILIO_TOKEN'),
        'sender' => env('SMS_DELIVERY_TWILIO_SENDER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Nexmo provider
    |--------------------------------------------------------------------------
    */
    'nexmo' => [
        'api_key' => env('SMS_DELIVERY_NEXMO_API_KEY'),
        'api_secret' => env('SMS_DELIVERY_NEXMO_API_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mobizon provider
    |--------------------------------------------------------------------------
    */
    'mobizon' => [
        'api_key' => env('SMS_DELIVERY_MOBIZON_API_KEY'),
        'api_server' => env('SMS_DELIVERY_MOBIZON_SERVER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS.ru provider
    |--------------------------------------------------------------------------
    */
    'sms_ru' => [
        'api_key' => env('SMS_DELIVERY_SMS_RU_API_KEY'),
    ]
];