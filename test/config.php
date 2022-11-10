<?php
/**
 * Project template-backend-package
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 02/07/2022
 * Time: 00:19
 */
require_once __DIR__ . '/../vendor/autoload.php';
$config = [
    'DATABASE' => [
        'driver'    => 'mysql',
        'host'      => 'mariadb',
        'username'  => 'root',
        'password'  => '150115',
        'database'  => 'base_api',
        'port'      => 3306,
        'prefix'    => 'tnv_',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'OPTIONS'  => [
        'showSignature'         => true,
        'debugStatus'           => true,
        'debugLevel'            => 'debug',
        'loggerPath'            => __DIR__ . '/../tmp/logs/',
        // Cache
        'cachePath'             => __DIR__ . '/../tmp/cache/',
        'cacheTtl'              => 3600,
        'cacheDriver'           => 'files',
        'cacheFileDefaultChmod' => 0777,
        'cacheSecurityKey'      => 'BACKEND-SERVICE',
        'aws'                   => [
            'version'     => 'latest',
            'region'      => 'reason',
            'credentials' => [
                'key'    => 'key bucket',
                'secret' => 'secret key',
            ],
        ]
    ]
];

use nguyenanhung\Backend\huynq_aws\Http\WebServiceAws;

$uploadData = [
    'Bucket'     => 'your-bucket',
    'Key'        => '2022-11-10/hippo',
    'SourceFile' => '/home/hippo/Desktop/20457177.jpg',
    'ACL'        => 'public-read',
];
$downloadData = array(
    'Bucket'   => 'your-bucket',
    'Key'      => 'hehe',
    'fileName' => '/hippo2.png',
    'path' =>'/etc/nginx/',
);

//upload file return url
$api = new WebServiceAws($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($uploadData)
    ->upload();

// download
$api = new WebServiceAws($config['OPTIONS']);
$api->setSdkConfig($config);
$api->setInputData($downloadData)
    ->download();

echo "<pre>";
print_r($api->getResponse());
echo "</pre>";



