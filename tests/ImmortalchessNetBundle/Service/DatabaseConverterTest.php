<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 21.08.16
 * Time: 0:45
 */

namespace ImmortachessNetBundle\Tests\Service;

use ImmortalchessNetBundle\Service\DatabaseConverter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DatabaseConverterTest
 * @package ImmortachessNetBundle\Tests\Service
 */
class DatabaseConverterTest extends KernelTestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var DatabaseConverter
     */
    private $service;

    public function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->service = $this->container->get('immortalchess.service.database_converter');
    }

    public function testShowTables()
    {
        $tables = $this->invokePrivateMethod('showTables');
        $this->assertGreaterThan(14, count($tables));
        self::assertContains('post', $tables);
    }

    public function testDumpIntoFile()
    {
        $fs = new Filesystem();
        $fs->remove(DatabaseConverter::DUMP_IN_FILE_NAME);

        $this->invokePrivateMethod('dumpTableIntoFile', ['forum']);
        self::assertFileExists(DatabaseConverter::DUMP_IN_FILE_NAME);
    }

    public function testConvertTextInFile()
    {
        $fs = new Filesystem();
        $fs->remove(DatabaseConverter::DUMP_OUT_FILE_NAME);

        $this->invokePrivateMethod(
            'convertTextInFile', [DatabaseConverter::DUMP_IN_FILE_NAME]
        );
        self::assertFileExists(DatabaseConverter::DUMP_OUT_FILE_NAME);
    }

    public function testRestoreFromFile()
    {
        $this->invokePrivateMethod('restoreFromFile', [DatabaseConverter::DUMP_OUT_FILE_NAME]);
    }

    public function testRun()
    {
        $this->service->run();
    }

    /**
     * @param string $methodName
     * @param array $params
     * @return mixed
     */
    private function invokePrivateMethod(string $methodName, array $params = [])
    {
        $method = new \ReflectionMethod(DatabaseConverter::class, $methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->service, $params);
    }
}