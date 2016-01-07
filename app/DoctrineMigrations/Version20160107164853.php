<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160107164853 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE hash hash VARCHAR(255) DEFAULT NULL, CHANGE confirm confirm TINYINT(1) DEFAULT NULL, CHANGE in_rest in_rest TINYINT(1) DEFAULT NULL, CHANGE left_rest left_rest INT DEFAULT NULL, CHANGE gone_in_rest gone_in_rest DATETIME DEFAULT NULL, CHANGE last_auth last_auth DATETIME DEFAULT NULL, CHANGE immortal_id immortal_id INT DEFAULT NULL, CHANGE another_login another_login VARCHAR(255) DEFAULT NULL, CHANGE last_move last_move DATETIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE hash hash VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE confirm confirm TINYINT(1) NOT NULL, CHANGE in_rest in_rest TINYINT(1) NOT NULL, CHANGE left_rest left_rest INT NOT NULL, CHANGE gone_in_rest gone_in_rest TIME NOT NULL, CHANGE last_auth last_auth TIME NOT NULL, CHANGE immortal_id immortal_id INT NOT NULL, CHANGE another_login another_login VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE last_move last_move TIME NOT NULL');
    }
}
