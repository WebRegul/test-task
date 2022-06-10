<?php

return [
    'passwords' => [
        'ttl' => env('GALLERY_PASSWORDS_LIFETIME', 1440), # in minutes: 60m * 24h = 1d = 1440m
        'salt' => env('GALLERY_PASSWORDS_SALT', ''),
    ],
    'sections' => [
        'default_title' => 'Новая секция',
    ],
    'galleries' => [
        'limit' => env('GALLERY_LIMIT', 100),
    ],
    'default_options' => [
        'font' => [
            'default' => 'Montserrat',
            'values' => ['Montserrat', 'Merriweather', 'Cormorant'],
        ], // шрифт: Montserrat|Merriweather|Cormorant
        'color' => [
            'default' => '#F4F1EE',
        ], // выбранный цвет
        'image_size' => [
            'default' => 'big',
            'values' => ['normal', 'big', 'large'],
        ], // размер изображений: normal|big|large
        'greed' => [
            'default' => 'vast',
            'values' => ['compact', 'vast'],
        ], // размер сетки: compact|vast
        'template' => [
            'default' => 'classic',
            'values' => ['vertical_photo', 'classic', 'fullscreen_photo', 'large_typography'],
        ], // тип темплейта (кубики внизу): vertical_photo|classic|fullscreen_photo|large_typography
    ],
    'main_galleries' =>  explode(',', env('MAIN_GALLERIES'))
];
