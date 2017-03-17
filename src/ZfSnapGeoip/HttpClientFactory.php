<?php

namespace ZfSnapGeoip;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Zend\Http\Client;

class HttpClientFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $adapter Zend\Http\Client\Adapter\AdapterInterface */
        $adapter = $container->get('ZfSnapGeoip\HttpClient\Adapter');

        $config = $container->get('config');
        $options = $config['maxmind']['http_client']['options'];

        $client = new Client();
        $client->setAdapter($adapter);
        $client->setOptions($options);

        return $client;
    }
    
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Client::class);
    }
}
