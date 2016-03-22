<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.01.16
 * Time: 22:55
 */

namespace ApiBundle\Controller;

use CoreBundle\Model\Request\RequestInterface;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Processor\ProcessorInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolation;

abstract class BaseController extends FOSRestController
{
    /**
     * @return ProcessorInterface
     */
    abstract protected function getProcessor();

    /**
     * @param Request $request
     * @return array
     */
    protected function getRequestParams(Request $request)
    {
        $jsonRequest = (array)json_decode($request->getContent(), true);

        switch (true) {
            case !empty($jsonRequest):
                $params = $jsonRequest;
                break;
            default:
                $params = $request->request->all();
                break;
        }

        $params = array_merge($params, $request->query->all());

        foreach ($request->attributes->all() as $name => $value) { // add url parameters such as {id}
            if (substr($name, 0, 1) != '_') {
                $params[$name] = $value;
            }
        }

        return $params;
    }

    /**
     * @param Request $request
     * @param RequestInterface $requestObject
     * @param int $successStatusCode
     * @return Response
     */
    protected function process(Request $request, RequestInterface $requestObject, $successStatusCode = ResponseStatusCode::OK)
    {
        $data = [];
        try {
            $requestMethod = strtolower($request->getMethod());
            $actionType = str_replace([$requestMethod, 'Action'], '', debug_backtrace()[1]['function']);
            $actionName = 'process' . ucfirst($requestMethod) . ucfirst($actionType);

            $errorRequestObject = clone $requestObject;
            $requestObject = $this->fillRequestObjectWithRequest($request, $requestObject);

            foreach ($this->container->get('validator')->validate($requestObject) as $error) {
                $errorRequestObject->{'set'.ucfirst($error->getPropertyPath())}($error->getMessage());
            }

            $this->container->get("core.service.error")->throwExceptionIfHasErrors($errorRequestObject, ResponseStatusCode::BAD_FORMAT);

            $data = $this->getProcessor()->$actionName($requestObject, $errorRequestObject);

            $statusCode = $successStatusCode;
        } catch (ProcessorException $exception) {
            $data = $exception->getRequestError();
            $statusCode = $exception->getCode();
        } catch (\Exception $exception) {
            $data['errorMessage'] = $exception->getMessage();
            if ($this->container->get('kernel')->getEnvironment() != 'prod') {
                $data['debug']['errorFile'] = $exception->getFile();
                $data['debug']['errorLine'] = $exception->getLine();
            }
            $statusCode = ResponseStatusCode::ISE;
        }

        return $this->handleView(
            $this->view($data, $statusCode)
        );
    }

    /**
     * @param Request $request
     * @param RequestInterface $requestObject
     * @return RequestInterface
     */
    private function fillRequestObjectWithRequest(Request $request, RequestInterface $requestObject)
    {
        $requestParams = $this->getRequestParams($request);

        foreach ($requestParams as $index => $param) {
            if (is_array($param) && isset($param['id'])) { // dropdown from angularjs
                $requestParams[$index] = $param['id'];
            }
        }

        $serializer = $this->container->get('jms_serializer');
        $requestObject = $serializer->deserialize(json_encode($requestParams), get_class($requestObject), 'json');

        return $requestObject;
    }
}