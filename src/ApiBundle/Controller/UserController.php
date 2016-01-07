<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.01.16
 * Time: 21:37
 */

namespace ApiBundle\Controller;

use CoreBundle\Exception\ProcessorException;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package ApiBundle\Controller
 * @RouteResource("User", pluralize=false)
 */
class UserController extends BaseController
{
    public function postRegisterAction(Request $request)
    {
        $params = $this->getRequestParams($request);

        try {
            $data['data'] = $this->container->get("core.handler.user")->processRegister($params);
            $statusCode = 200;
        } catch (ProcessorException $exception) {
            $data['errors'] = $exception->getErrors();
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