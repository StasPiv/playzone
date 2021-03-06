<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.01.16
 * Time: 22:55
 */

namespace ApiBundle\Controller;

use CoreBundle\Exception\Processor\ProcessorExceptionInterface;
use CoreBundle\Model\Request\RequestError;
use CoreBundle\Model\Request\RequestInterface;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Processor\ProcessorInterface;
use Doctrine\Common\Util\Inflector;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * Class BaseController
 * @package ApiBundle\Controller
 */
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
    protected function process(
        Request $request, 
        RequestInterface $requestObject, 
        $successStatusCode = ResponseStatusCode::OK
    )
    {
        $data = [];
        try {
            $requestMethod = strtolower($request->getMethod());
            $actionType = str_replace([$requestMethod, 'Action'], '', debug_backtrace()[1]['function']);
            $actionName = 'process' . ucfirst($requestMethod) . ucfirst($actionType);

            $requestObject = $this->fillRequestObjectWithRequest($request, $requestObject);

            foreach ($this->container->get('validator')->validate($requestObject) as $error) {
                /** @var ConstraintViolation $error */
                $this->container->get("core.request.error")->addError(strtolower(preg_replace("/([A-Z])/", "_$1", $error->getPropertyPath())), $error->getMessage());
            }

            $this->container->get("core.service.error")->throwExceptionIfHasErrors($this->container->get("core.request.error"), ResponseStatusCode::BAD_FORMAT);

            $data = $this->getProcessor()->$actionName($requestObject);

            $statusCode = $successStatusCode;
        } catch (ProcessorExceptionInterface $exception) {
            if ($exception instanceof ProcessorException) {
                $data = $exception->getRequestErrorInterface()->getErrors();
            } else {
                $data = $exception->getMessage();
            }
            $statusCode = $exception->getCode();
        } catch (\Exception $exception) {
            $data['errorMessage'] = $exception->getMessage();
            if ($this->container->get('kernel')->getEnvironment() != 'prod') {
                $data['debug']['errorFile'] = $exception->getFile();
                $data['debug']['errorLine'] = $exception->getLine();
                $data['debug']['errorType'] = get_class($exception);
            }
            $statusCode = ResponseStatusCode::ISE;
        }

        $view = $this->view($data, $statusCode);

        $view->getContext()->setGroups(["Default", $request->get('_route')]);

        return $this->handleView($view);
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

        $requestObject->setIp($request->getClientIp());

        return $requestObject;
    }
}