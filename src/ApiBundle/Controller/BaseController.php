<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.01.16
 * Time: 22:55
 */

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends FOSRestController
{

    /**
     * Converts view into a response object.
     *
     * Not necessary to use, if you are using the "ViewResponseListener", which
     * does this conversion automatically in kernel event "onKernelView".
     *
     * @param View $view
     *
     * @return Response
     */
    protected function handleView(View $view)
    {
        $view->setData(
            array_merge(
                ['status' => $view->getStatusCode()],
                $view->getData()
            )
        );
        $response = parent::handleView($view);

        if (!isset($_SERVER['HTTP_HOST'])) {
            return $response;
        }

        $allowedDomain = 'http://' . preg_replace('/^(api\.)/','', $_SERVER['HTTP_HOST']);
        $response->headers->set('Access-Control-Allow-Origin', $allowedDomain);
        return $response;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getRequestParams(Request $request)
    {
        $jsonRequest = (array)json_decode($request->getContent(), true);

        if (empty($jsonRequest)) {
            return $request->request->all();
        }

        return $jsonRequest;
    }
}