<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 28.05.16
 * Time: 14:32
 */

namespace CoreBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class DateService
 * @package CoreBundle\Service
 */
class DateService
{
    use ContainerAwareTrait;
    
    /**
     * @param string $time
     * @return \DateTime
     */
    public function getDateTime(string $time = "now") : \DateTime
    {
        return new \DateTime($time, $this->getDefaultTimezone());
    }

    /**
     * @param string $time
     * @return \DateTime
     */
    public function getLondonDateTime(string $time = "now") : \DateTime
    {
        return new \DateTime($time, $this->getLondonTimezone());
    }

    /**
     * @return \DateTimeZone
     */
    public function getDefaultTimezone()
    {
        return new \DateTimeZone($this->container->getParameter('app_core_timezone'));
    }

    /**
     * @return \DateTimeZone
     */
    public function getLondonTimezone()
    {
        return new \DateTimeZone('Europe/London');
    }
}