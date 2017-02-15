<?php
return [

    'apns' => [
        'environment' => \Origami\Push\Drivers\Apns::SANDBOX,
        'certificate' => env('PUSH_APNS_CERTIFICATE', storage_path('certificates/push.pem')),
        'cafile' => storage_path('certificates/entrust_2048_ca.cer'),
        'passphrase' => env('PUSH_APNS_PASSPHRASE', ''),
    ],

    'gcm' => [
        'key' => env('PUSH_GCM_KEY', ''),
    ],

    'fcm' => [
        'key' => env('PUSH_FCM_KEY', ''),
    ],

];