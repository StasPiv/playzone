<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.01.16
 * Time: 22:38
 */

namespace ApiBundle\Controller;

use CoreBundle\Model\Request\Timecontrol\TimecontrolGetRequest;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CoreBundle\Processor\TimecontrolProcessorInterface;

/**
 * Class TimecontrolController
 * @package ApiBundle\Controller
 * @RouteResource("Timecontrols", pluralize=true)
 */
class TimecontrolController extends BaseController
{
    public function getAction(Request $request)
    {
        return $this->process($request, new TimecontrolGetRequest());
    }

    /**
     * @return TimecontrolProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->container->get("core.handler.timecontrol");
    }
}