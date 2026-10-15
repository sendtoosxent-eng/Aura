<?php

return [
    'key_id' => env('APNS_KEY_ID'),
    'team_id' => env('APNS_TEAM_ID'),
    'bundle_id' => env('APNS_BUNDLE_ID', 'ug.afyalink.ios'),
    'private_key_path' => env('APNS_PRIVATE_KEY_PATH'),
];
