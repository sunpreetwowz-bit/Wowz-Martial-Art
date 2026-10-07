<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Academy Settings
    |--------------------------------------------------------------------------
    */
    'name' => env('ACADEMY_NAME', 'Wowz Martial Art'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Kolkata'),
    'phone' => env('ACADEMY_PHONE', '+91 98765 43210'),
    'phone_alt' => env('ACADEMY_PHONE_ALT', '+91 98765 43211'),
    'email' => env('ACADEMY_EMAIL', 'hello@wowzmartialart.test'),
    'hours' => env('ACADEMY_HOURS', 'Mon–Sat · 6:00 AM – 9:00 PM'),

    /*
    | Training locations shown on contact, footer, and event venues.
    */
    'branches' => [
        [
            'key' => 'sector-46',
            'name' => 'Kundan International School',
            'area' => 'Sector 46, Chandigarh',
            'address' => 'Kundan International School, Sector 46, Chandigarh',
            'city' => 'Chandigarh',
            'note' => 'Kids & school-hour batches',
            'lat' => 30.7059,
            'lng' => 76.7473,
            'map_query' => 'Kundan International School, Sector 46, Chandigarh',
        ],
        [
            'key' => 'sector-27',
            'name' => 'Ramgharia Bhawan',
            'area' => 'Sector 27, Chandigarh',
            'address' => 'Ramgharia Bhawan, Sector 27, Chandigarh',
            'city' => 'Chandigarh',
            'note' => 'Evening taekwondo & kickboxing',
            'lat' => 30.7215,
            'lng' => 76.7768,
            'map_query' => 'Ramgarhia Bhawan, Sector 27, Chandigarh',
        ],
        [
            'key' => 'zirakpur',
            'name' => 'Intensity Martial Art and Fitness',
            'area' => 'Zirakpur',
            'address' => 'Intensity Martial Art and Fitness, Zirakpur',
            'city' => 'Zirakpur',
            'note' => 'Full academy training floor',
            'lat' => 30.6425,
            'lng' => 76.8173,
            'map_query' => 'Intensity Martial Art and Fitness, Zirakpur',
        ],
    ],

    // Kept for backwards compatibility in older views
    'address' => env('ACADEMY_ADDRESS', 'Kundan International School, Sector 46 · Ramgharia Bhawan, Sector 27 · Intensity Martial Art and Fitness, Zirakpur'),

    /*
    | Application forms close this many minutes before test start
    | when application_closes_at is not explicitly set.
    */
    'belt_test_application_close_minutes' => (int) env('BELT_TEST_CLOSE_MINUTES', 30),

    'certificate_prefix' => env('CERTIFICATE_PREFIX', 'CERT'),
    'student_code_prefix' => env('STUDENT_CODE_PREFIX', 'WMA'),

    /*
    | Database notifications are always enabled. Optional mail channel is
    | queueable when enabled — keep false until SMTP is configured.
    */
    'notifications' => [
        'mail' => (bool) env('ACADEMY_NOTIFY_MAIL', false),
        'deadline_hours' => (int) env('ACADEMY_DEADLINE_NOTIFY_HOURS', 24),
    ],

    /*
    | When set, /payments/callback requires payload signature = hash_hmac
    | sha256 of payment_uuid|status|transaction_id using this secret.
    */
    'payments' => [
        'callback_secret' => env('PAYMENT_CALLBACK_SECRET'),
    ],
];
