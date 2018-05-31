<?php

use \Charcoal\App\AppConfig;
use \Charcoal\App\AppContainer;
use \Charcoal\Config\GenericConfig;

$autoloader = require __DIR__.'/../vendor/autoload.php';
$autoloader->add('Charcoal\\', __DIR__.'/src/');
$autoloader->add('Charcoal\\Tests\\', __DIR__);

$config = new AppConfig([
    'base_path' => (dirname(__DIR__).'/'),
    'databases' => [
        'phpunit' => [
            'type'     => 'sqlite',
            'database' => 'charcoal_test',
            'username' => 'root',
            'password' => ''
        ]
    ],
    'default_database' => 'phpunit',
    'metadata' => [
        'paths' => [
            'metadata/',
            'vendor/locomotivemtl/charcoal-app/metadata/',
            'vendor/locomotivemtl/charcoal-property/metadata/',
            'vendor/locomotivemtl/charcoal-base/metadata/'
        ]
    ],
    'service_providers' => [
        'charcoal/email/service-provider/email' => [],
        'charcoal/model/service-provider/model' => []
    ],
    'email' => [
        'defaultFrom' => 'charcoal@example.com'
    ]
]);

$GLOBALS['container'] = new AppContainer([
    'config' => $config
]);
