<?php

// Append this array entry into config/services.php under the returned array.
// Shown standalone here for clarity — merge into your actual services.php.

return [

    'meta' => [
        'app_id' => env('META_APP_ID'),
        'app_secret' => env('META_APP_SECRET'),
        'whatsapp_token' => env('META_WHATSAPP_TOKEN'),
        'whatsapp_phone_number_id' => env('META_WHATSAPP_PHONE_NUMBER_ID'),
        'webhook_verify_token' => env('META_WEBHOOK_VERIFY_TOKEN'),
    ],

];
