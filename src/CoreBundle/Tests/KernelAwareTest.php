<?php

namespace CoreBundle\Tests;

use AppKernel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\DependencyInjection\Container;

//use Symfony\Component\HttpKernel\AppKernel;

require_once dirname(__DIR__).'/../../app/AppKernel.php';

/**
 * Test case class helpful with Entity tests requiring the database interaction.
 * For regular entity tests it's better to extend standard \PHPUnit_Framework_TestCase instead.
 */
abstract class KernelAwareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AppKernel
     */
    protected $kernel;

    /**
     * @var EntityManager
     */
    protected $objectManager;

    /**
     * @var Container
     */
    protected $container;

    /**
     */
    protected function setUp()
    {
        $this->kernel = new AppKernel('test', true);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
        $this->objectManager = $this->container->get('doctrine')->getManager();

        //$this->generateSchema();

        parent::setUp();
    }

    /**
     */
    protected function tearDown()
    {
        $this->kernel->shutdown();

        $refl = new \ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }

        parent::tearDown();
    }

    /**
     */
    protected function generateSchema()
    {
        $metaData = $this->getMetaData();

        if (!empty($metaData)) {
            $tool = new SchemaTool($this->objectManager);
            $tool->dropSchema($metaData);
            $tool->createSchema($metaData);
        }
    }

    /**
     * @return array
     */
    protected function getMetaData()
    {
        return $this->objectManager->getMetadataFactory()->getAllMetadata();
    }
}