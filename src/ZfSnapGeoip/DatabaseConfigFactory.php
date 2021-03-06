<?php

namespace ZfSnapGeoip;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Factory of DatabaseConfig
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class DatabaseConfigFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $data = $config['maxmind']['database'];

        return new DatabaseConfig($data);
    }
    
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, DatabaseConfig::class);
    }
}