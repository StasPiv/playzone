<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 20:19
 */

namespace ApiBundle\Model\Request\Game;

use ApiBundle\Model\Request\RequestInterface;
use CoreBundle\Exception\Processor\GameProcessorException;
use CoreBundle\Exception\Processor\ProcessorException;

abstract class GameRequest implements RequestInterface
{
    protected $errorMessage = "Game request failed";

    /**
     * @param int $code
     * @param array $errors
     * @param string $message
     * @param \Exception $previous
     * @return ProcessorException
     */
    public function getException($code = 0, array $errors = [], $message = "", \Exception $previous = null)
    {
        return new GameProcessorException($this->errorMessage, $code, $errors, $previous);
    }

}