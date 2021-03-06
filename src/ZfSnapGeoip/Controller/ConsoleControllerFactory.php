<?php

namespace ZfSnapGeoip\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use ZfSnapGeoip\DatabaseConfig;

/**
 * Factory of ConsoleController
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class ConsoleControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $console = $container->get('Console');
        $config = $container->get(DatabaseConfig::class);
        $httpClient = $container->get('ZfSnapGeoip\HttpClient');

        return new $requestedName($console, $config, $httpClient);
    }
    
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ConsoleController::class);
    }
}
