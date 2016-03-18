<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160319002552 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game_call DROP FOREIGN KEY FK_279B4312A80B2D8E');
        $this->addSql('DROP INDEX IDX_279B4312A80B2D8E ON game_call');
        $this->addSql('ALTER TABLE game_call DROP id_game');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game_call ADD id_game INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game_call ADD CONSTRAINT FK_279B4312A80B2D8E FOREIGN KEY (id_game) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_279B4312A80B2D8E ON game_call (id_game)');
    }
}
