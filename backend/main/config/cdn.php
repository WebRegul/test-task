<?php

return [
    # проброс всей статики, скриптов, стилей, шрифтов, иконок через
    # замену ASSET_URL в .env
    'img' => [
        # проброс всех изображений
        'default' => env('CDN_IMG_DEFAULT', env('APP_URL')),
    ],
];
