<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 10.08.16
 * Time: 21:31
 */

namespace CoreBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserProblem
 * @package CoreBundle\Entity
 *
 * @ORM\Entity
 */
class UserProblem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\SerializedName("id")
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @var Problem
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\Problem")
     *
     * @ORM\ManyToOne(targetEntity="Problem", fetch="EAGER")
     * @ORM\JoinColumn(name="problem_id", referencedColumnName="id", nullable=true)
     */
    private $problem;

    /**
     * @var User
     *
     * @JMS\Expose
     * @JMS\Type("CoreBundle\Entity\User")
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $solved = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $total = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="float")
     */
    private $percent = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Problem
     */
    public function getProblem(): Problem
    {
        return $this->problem;
    }

    /**
     * @param Problem $problem
     * @return UserProblem
     */
    public function setProblem(Problem $problem): UserProblem
    {
        $this->problem = $problem;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return UserProblem
     */
    public function setUser(User $user): UserProblem
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return int
     */
    public function getSolved(): int
    {
        return $this->solved;
    }

    /**
     * @param int $solved
     * @return UserProblem
     */
    public function setSolved(int $solved): UserProblem
    {
        $this->solved = $solved;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     * @return UserProblem
     */
    public function setTotal(int $total): UserProblem
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return int
     */
    public function getPercent(): int
    {
        return $this->percent;
    }

    /**
     * @param int $percent
     * @return UserProblem
     */
    public function setPercent(int $percent): UserProblem
    {
        $this->percent = $percent;

        return $this;
    }
    
}