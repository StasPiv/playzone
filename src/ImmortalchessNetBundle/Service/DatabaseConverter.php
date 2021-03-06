<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 21.08.16
 * Time: 0:10
 */

namespace ImmortalchessNetBundle\Service;

use Doctrine\DBAL\Connection;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class DatabaseConverter
 * @package ImmortalchessNetBundle\Service
 */
class DatabaseConverter
{
    const DUMP_IN_FILE_NAME = '/tmp/in.sql';

    const DUMP_OUT_FILE_NAME = '/tmp/out.sql';

    /**
     * @var TextConverter
     */
    private $textConverter;

    /**
     * @var string
     */
    private $mysqlUsername;
    /**
     * @var string
     */
    private $mysqlPassword;
    /**
     * @var string
     */
    private $mysqlDbname;
    /**
     * @var ProcessBuilder
     */
    private $processBuilder;
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var Registry
     */
    private $doctrine;
    /**
     * @var string
     */
    private $mysqlHost;
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * DatabaseConverter constructor.
     * @param TextConverter $textConverter
     * @param string $mysqlHost
     * @param string $mysqlUsername
     * @param string $mysqlPassword
     * @param string $mysqlDbname
     * @param Registry $doctrine
     * @param Filesystem $fs
     */
    public function __construct(
        TextConverter $textConverter,
        string $mysqlHost,
        string $mysqlUsername,
        string $mysqlPassword,
        string $mysqlDbname,
        Registry $doctrine,
        Filesystem $fs
    )
    {
        $this->textConverter = $textConverter;
        $this->initMysqlSettings($mysqlHost, $mysqlUsername, $mysqlPassword, $mysqlDbname);
        $this->processBuilder = new ProcessBuilder();
        $this->doctrine = $doctrine;
        $this->fs = $fs;
    }

    /**
     * @param string $connectionName
     * @param string $mysqlHost
     * @param string $mysqlUsername
     * @param string $mysqlPassword
     * @param string $mysqlDbname
     * @param array $onlyTables
     */
    public function run(
        string $connectionName = 'default',
        string $mysqlHost = 'localhost',
        string $mysqlUsername = null,
        string $mysqlPassword = null,
        string $mysqlDbname = null,
        array $onlyTables = []
    )
    {
        $this->connection = $this->doctrine->getConnection($connectionName);

        if ($mysqlHost) {
            $this->initMysqlSettings($mysqlHost, $mysqlUsername, $mysqlPassword, $mysqlDbname);
        }

        $tables = $this->showTables();

        foreach ($tables as $tableName) {
            $this->fs->remove([self::DUMP_IN_FILE_NAME, self::DUMP_OUT_FILE_NAME]);

            if (!empty($onlyTables) && !in_array($tableName, $onlyTables)) {
                continue;
            }

            $this->dumpTableIntoFile($tableName);
            $this->convertTextInFile(self::DUMP_IN_FILE_NAME);
            $this->restoreFromFile(self::DUMP_OUT_FILE_NAME);
        }
    }

    /**
     * @param string $tableName
     * @return DatabaseConverter
     */
    private function dumpTableIntoFile(string $tableName): self
    {
        $command = 'mysqldump --replace -h'.$this->mysqlHost.' -u'.$this->mysqlUsername.' -p'.$this->mysqlPassword.' '.$this->mysqlDbname.' '.$tableName.' > '.self::DUMP_IN_FILE_NAME;

        exec($command);

        return $this;
    }

    /**
     * @return array table names
     */
    private function showTables(): array
    {
        $tables = $this->connection->fetchAll('SHOW TABLES');

        return array_map(
            function ($table)
            {
                return $table['Tables_in_'.$this->mysqlDbname];
            },
            $tables
        );
    }

    /**
     * @param string $fileName
     * @return DatabaseConverter
     */
    private function convertTextInFile(string $fileName): self
    {
        $this->textConverter->convertTextFile($fileName, self::DUMP_OUT_FILE_NAME);

        return $this;
    }

    /**
     * @param string $fileName
     * @return DatabaseConverter
     */
    private function restoreFromFile(string $fileName): self
    {
        $command = 'mysql -h'.$this->mysqlHost.' -u'.$this->mysqlUsername.' -p'.$this->mysqlPassword.' '.$this->mysqlDbname.' < '.$fileName;

        exec($command);

        return $this;
    }

    /**
     * @param string $mysqlHost
     * @param string $mysqlUsername
     * @param string $mysqlPassword
     * @param string $mysqlDbname
     */
    private function initMysqlSettings(
        string $mysqlHost,
        string $mysqlUsername,
        string $mysqlPassword,
        string $mysqlDbname
    ) {
        $this->mysqlHost = $mysqlHost;
        $this->mysqlUsername = $mysqlUsername;
        $this->mysqlPassword = $mysqlPassword;
        $this->mysqlDbname = $mysqlDbname;
    }
}