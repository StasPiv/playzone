<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 17:14
 */

namespace CoreBundle\Model\Request\User;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserGetListRequest
 * @package CoreBundle\Model\Request\User
 */
class UserGetListRequest extends UserRequest
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $orderBy = "u.login";

    /**
     * @var integer
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    private $limit = 20;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    private $filter = '';

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     * @return UserGetListRequest
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return UserGetListRequest
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilter(): string
    {
        return $this->filter;
    }

    /**
     * @param string $filter
     * @return UserGetListRequest
     */
    public function setFilter(string $filter): self
    {
        $this->filter = $filter;

        return $this;
    }
}