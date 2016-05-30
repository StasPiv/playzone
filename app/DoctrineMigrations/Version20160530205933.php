<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160530205933 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tournament_game DROP INDEX UNIQ_14A683B2CB68070C, ADD INDEX IDX_14A683B2CB68070C (player_white_id)');
        $this->addSql('ALTER TABLE tournament_game DROP INDEX UNIQ_14A683B2D530A29C, ADD INDEX IDX_14A683B2D530A29C (player_black_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tournament_game DROP INDEX IDX_14A683B2CB68070C, ADD UNIQUE INDEX UNIQ_14A683B2CB68070C (player_white_id)');
        $this->addSql('ALTER TABLE tournament_game DROP INDEX IDX_14A683B2D530A29C, ADD UNIQUE INDEX UNIQ_14A683B2D530A29C (player_black_id)');
    }
}
