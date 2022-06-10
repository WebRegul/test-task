<?php

return [
    'default' => env('FILESYSTEM_DRIVER', 'local'),
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'permissions' => [
                'file' => [
                    'public' => 0664,
                    'private' => 0600,
                    'readonly' => 0444,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0700,
                    'readonly' => 0444,
                ],
            ],
        ],
        'users' => [
            'driver' => 'local',
            'root' => storage_path('app/users'),
            'permissions' => [
                'file' => [
                    'public' => 0664,
                    'private' => 0600,
                    'readonly' => 0444,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0700,
                    'readonly' => 0444,
                ],
            ],
        ],
        'downloads' => [
            'driver' => 'local',
            'root' => storage_path('app/downloads'),
            'permissions' => [
                'file' => [
                    'public' => 0664,
                    'private' => 0600,
                    'readonly' => 0444,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0700,
                    'readonly' => 0444,
                ],
            ],
        ],
        'test-images' => [
            'driver' => 'local',
            'root' => storage_path('test-images'),
            'permissions' => [
                'file' => [
                    'public' => 0664,
                    'private' => 0600,
                    'readonly' => 0444,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0700,
                    'readonly' => 0444,
                ],
            ],
        ],
    ],
];
