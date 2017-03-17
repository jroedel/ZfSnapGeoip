<?php
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Hydrator\ClassMethods;
use Zend\Http\Client\Adapter\Curl;
use ZfSnapGeoip\Controller;
use ZfSnapGeoip\DatabaseConfig;
use ZfSnapGeoip\DatabaseConfigFactory;
use ZfSnapGeoip\Entity;
use ZfSnapGeoip\Service;

return [
    'maxmind' => [
        'database' => [
            'source' => 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz',
            'destination' => __DIR__ . '/../data/',
            'filename' => 'GeoLiteCity.dat',
            'flag' => GEOIP_STANDARD,
            'regionvars' => __DIR__ . '/../../../geoip/geoip/src/geoipregionvars.php',
        ],
        'http_client' => [
            'options' => [
                'timeout' => 300,
            ],
        ],
        'timezone_function_path' => __DIR__ . '/../../../geoip/geoip/src/timezone.php',
    ],
    'service_manager' => [
        'invokables' => [
            'ZfSnapGeoip\HttpClient\Adapter' => Curl::class,
        ],
        'factories' => [
            ClassMethods::class => InvokableFactory::class,
            Entity\Record::class => InvokableFactory::class,
            Service\Geoip::class => Service\GeoipFactory::class,
            DatabaseConfig::class => DatabaseConfigFactory::class,
            'ZfSnapGeoip\HttpClient' => 'ZfSnapGeoip\HttpClientFactory',
        ],
        'aliases' => [
            'geoip' => Service\Geoip::class,
            'geoip_record' => Entity\Record::class,
            'geoip_hydrator' => ClassMethods::class
        ],
        'shared' => [
            Entity\Record::class => false,
            'geoip_record' => false,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            \ZfSnapGeoip\View\Helper\Geoip::class => \ZfSnapGeoip\View\Helper\GeoipFactory::class,
        ],
        'aliases' => [
            'geoip' => \ZfSnapGeoip\View\Helper\Geoip::class,
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\Console::class => Controller\ConsoleControllerFactory::class,
        ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'geoip-download' => [
                    'options' => [
                        'route' => ZfSnapGeoip\Module::CONSOLE_GEOIP_DOWNLOAD,
                        'defaults' => [
                            'controller' => Controller\Console::class,
                            'action' => 'download',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
