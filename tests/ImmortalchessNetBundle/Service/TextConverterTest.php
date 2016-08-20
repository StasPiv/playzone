<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 19.08.16
 * Time: 0:23
 */

namespace ImmortachessNetBundle\Tests\Service;

use ImmortalchessNetBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class TextConvertorTest
 * @package ImmortachessNetBundle\Tests\Service
 */
class TextConverterTest extends KernelTestCase
{

    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->service = $this->container->get("immortalchessnet.service.immortalchessnet");
    }

    public function testConvertDatabase()
    {
        /** @var Post[] $posts */
        $posts = $this->getManager()->getRepository('ImmortalchessNetBundle:Post')->findAll();

        foreach ($posts as $post) {
            $post->setTitle($this->convertTextOpposite($post->getTitle()))
                 ->setPagetext($this->convertTextOpposite($post->getPagetext()));

            $this->getManager()->persist($post);
        }

        $this->getManager()->flush();
    }

    public function testConvertTextFile()
    {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . 'test.txt';

        $this->getTextConverter()->convertTextFile($filename);

        self::assertEquals('Валекс', file_get_contents($filename));
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getManager()
    {
        return $this->container->get('doctrine')->getManager('immortalchess');
    }

    /**
     * @param string $text
     * @return string
     */
    protected function convertTextOpposite(string $text)
    {
        return $this->getTextConverter()->convertTextToNormal($text);
    }

    /**
     * @return \ImmortalchessNetBundle\Service\TextConverter|object
     */
    protected function getTextConverter()
    {
        return $this->container->get('immortalchessnet.service.text.converter');
    }
}