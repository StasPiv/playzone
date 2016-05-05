<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160505214429 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("UPDATE chat_message SET type = 1 WHERE type = 'common'");
        $this->addSql("UPDATE chat_message SET type = 2 WHERE type = 'game'");

        $this->addSql('ALTER TABLE chat_message CHANGE type type INT NOT NULL');
        $this->addSql('CREATE INDEX type_idx ON chat_message (type)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX type_idx ON chat_message');
        $this->addSql('ALTER TABLE chat_message CHANGE type type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');

        $this->addSql("UPDATE chat_message SET type = 'common' WHERE type = 1");
        $this->addSql("UPDATE chat_message SET type = 'game' WHERE type = 2");
    }
}
