<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.08.16
 * Time: 1:20
 */

namespace CoreBundle\Service\Chess;


use CoreBundle\Entity\Problem;
use CoreBundle\Service\Chess\Pgn\PgnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class ImportProblemsService
 * @package CoreBundle\Service\Chess
 */
class ImportProblemsService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * UserHandler constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:Problem');
    }

    /**
     * @param string $fileName
     * @return int
     */
    public function import(string $fileName) : int
    {
        if (!file_exists($fileName)) {
            throw new FileNotFoundException;
        }

        $parser = new PgnParser($fileName);
        
        $count = 0;
        
        foreach ($parser->getGames() as $pgnGame) {
            if ($pgnGame->getBlack() != '#2' || empty($pgnGame->getMoves())) {
                continue;
            }

            $count++;

            $problem = (new Problem())
                ->setFen($pgnGame->getFen())
                ->setPgn($pgnGame->getMoves());
            
            $this->manager->persist($problem);
        }

        $this->manager->flush();
        
        return $count;
    }
}