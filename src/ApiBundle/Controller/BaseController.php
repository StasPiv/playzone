<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.01.16
 * Time: 22:55
 */

namespace ApiBundle\Controller;

use CoreBundle\Exception\ProcessorException;
use CoreBundle\Processor\ProcessorInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends FOSRestController
{
    /**
     * @return ProcessorInterface
     */
    abstract protected function getProcessor();

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

    /**
     * @param Request $request
     * @param int $successStatusCode
     * @return Response
     */
    protected function process(Request $request, $successStatusCode = 200)
    {
        $data = [];
        try {
            $requestMethod = strtolower($request->getMethod());
            $actionType = str_replace([$requestMethod, 'Action'], '', debug_backtrace()[1]['function']);
            $actionName = 'process' . ucfirst($requestMethod) . ucfirst($actionType);
            $data['data'] = $this->getProcessor()->$actionName($this->getRequestParams($request));
            $statusCode = $successStatusCode;
        } catch (ProcessorException $exception) {
            if (!empty($exception->getErrors())) {
                $data['errors'] = $exception->getErrors();
            }
            $data['errorMessage'] = $exception->getMessage();
            $statusCode = $exception->getCode();
        } catch (\Exception $exception) {
            $data['errors'] = [];
            $data['errorMessage'] = $exception->getMessage();
            if ($this->container->get('kernel')->getEnvironment() == 'dev') {
                $data['errorFile'] = $exception->getFile();
                $data['errorLine'] = $exception->getLine();
            }
            $statusCode = 500;
        }

        return $this->handleView(
            $this->view($data, $statusCode)
        );
    }
}