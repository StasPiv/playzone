<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160418124652 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO user_setting (name, type) VALUES (\'Sound move\', \'checkbox\');');

        $this->addSql('INSERT INTO user_setting (name, type) VALUES (\'Sound draw\', \'checkbox\');');

        $this->addSql('INSERT INTO user_setting (name, type) VALUES (\'Sound win\', \'checkbox\');');

        $this->addSql('INSERT INTO user_setting (name, type) VALUES (\'Sound call\', \'checkbox\');');

        $this->addSql('INSERT INTO user_setting (name, type) VALUES (\'Sound new game\', \'checkbox\');');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM user_setting');
    }
}