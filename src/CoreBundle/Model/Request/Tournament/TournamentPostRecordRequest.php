<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 12:30
 */

namespace CoreBundle\Model\Request\Tournament;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TournamentPostRecordRequest
 * @package CoreBundle\Model\Request\Tournament
 */
class TournamentPostRecordRequest extends TournamentRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;
    
    /**
     * @var int
     *
     * @JMS\Expose()
     * @JMS\Type("integer")
     *
     * @Assert\NotBlank(
     *     message = "Tournament id is required for this request"
     * )
     */
    private $tournamentId;

    /**
     * @return int
     */
    public function getTournamentId() : int
    {
        return $this->tournamentId;
    }

    /**
     * @param int $tournamentId
     * @return TournamentPostRecordRequest
     */
    public function setTournamentId(int $tournamentId)
    {
        $this->tournamentId = $tournamentId;

        return $this;
    }
}