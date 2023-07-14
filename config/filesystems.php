<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'url'        => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key'    => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

        'admin' => [
            'driver'           => 'admin',
            'host'             => env('ADMIN_HOST'),
            'port'             => env('ADMIN_PORT', '22'),
            'username'         => env('ADMIN_USERNAME'),
            'password'         => env('ADMIN_PASSWORD'),
            'privateKey'       => env('ADMIN_PRIVATE_KEY', ''),
            'root'             => env('ADMIN_ROOT'),
            'timeout'          => env('ADMIN_TIMEOUT', '10'),
            'directoryPerm'    => env('ADMIN_DIRECTORY_PERM', 0777),
            'uploadImgMaxSize' => env('ADMIN_UPLOAD_IMG_MAX_SIZE', 5 * 1024 * 1024),
        ],

        'cosv5' => [
            'driver'          => 'cosv5',
            'region'          => env('COSV5_REGION', 'ap-shanghai'),//后面是控制台储存桶里设置的所属地域
            'credentials'     => [
                'appId'     => env('COSV5_APP_ID'),
                'secretId'  => env('COSV5_SECRET_ID'),
                'secretKey' => env('COSV5_SECRET_KEY'),
            ],
            'timeout'         => env('COSV5_TIMEOUT', 60),
            'connect_timeout' => env('COSV5_CONNECT_TIMEOUT', 60),
            'bucket'          => env('COSV5_BUCKET'),
            'cdn'             => env('COSV5_CDN'),
            'scheme'          => env('COSV5_SCHEME', 'https'),
            'read_from_cdn'   => env('COSV5_READ_FROM_CDN', false),
            'uploadImgMaxSize' => env('ADMIN_UPLOAD_IMG_MAX_SIZE', 10 * 1024 * 1024),
            'uploadAudioMaxSize' => env('ADMIN_UPLOAD_AUDIO_MAX_SIZE', 100 * 1024 * 1024),
            'uploadVideoMaxSize' => env('ADMIN_UPLOAD_VIDEO_MAX_SIZE', 500 * 1024 * 1024),
        ],

    ],

];
