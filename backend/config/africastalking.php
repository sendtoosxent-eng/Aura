<?php

return [

    'driver' => env('AFYALINK_SMS_DRIVER', 'log'),

    'username' => env(
        'AFRICASTALKING_USERNAME',
        'sandbox'
    ),

    'api_key' => env(
        'AFRICASTALKING_API_KEY'
    ),

    'sender_id' => env(
        'AFRICASTALKING_SENDER_ID'
    ),

];
