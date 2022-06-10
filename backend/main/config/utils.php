<?php

return [
    'default_date_format' => 'd.m.Y',
    'images_size_limit' => env('IMAGES_SIZE_LIMIT', 25000000),
    'max_section_count' => env('MAX_SECTION_COUNT', 32),
    'set_section_cache_time' => env('SET_SECTION_CACHE_TIME', 30),
    'drop_images_cache_time' => env('DROP_IMAGES_CACHE_TIME', 60),
    'drop_section_cache_time' => env('DROP_SECTION_CACHE_TIME', 60),
    'new_phone_cache_time' => env('NEW_PHONE_CACHE_TIME', 180),
    'reset_password_cache_time' => env('RESET_PASSWORD_CACHE_TIME', 60),
    'show_notifications_limit' => env('SHOW_NOTIFICATIONS_LIMIT', 10),
];
