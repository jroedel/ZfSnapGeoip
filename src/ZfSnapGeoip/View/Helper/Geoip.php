<?php

namespace ZfSnapGeoip\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfSnapGeoip\Service\Geoip as GeoipService;

/**
 * Geoip view helper
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class Geoip extends AbstractHelper
{
    /**
     * @var \ZfSnapGeoip\Service\Geoip
     */
    private $geoip;

    /**
     * Geoip view helper constructor.
     * @param GeoipService $geoip
     */
    public function __construct(GeoipService $geoip)
    {
        $this->geoip = $geoip;
    }
    
    /**
     * @param string $ipAddress
     * @return \ZfSnapGeoip\Service\Geoip
     */
    public function __invoke($ipAddress = null)
    {
        return $this->geoip->getRecord($ipAddress);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->geoip->getRecord();
    }
}
