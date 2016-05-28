<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160528195722 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tournament ADD rounds INT NOT NULL, ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tournament_game ADD player_white_id INT DEFAULT NULL, ADD player_black_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament_game ADD CONSTRAINT FK_14A683B2CB68070C FOREIGN KEY (player_white_id) REFERENCES tournament_player (id)');
        $this->addSql('ALTER TABLE tournament_game ADD CONSTRAINT FK_14A683B2D530A29C FOREIGN KEY (player_black_id) REFERENCES tournament_player (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_14A683B2CB68070C ON tournament_game (player_white_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_14A683B2D530A29C ON tournament_game (player_black_id)');
        $this->addSql('ALTER TABLE game CHANGE status status VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game CHANGE status status LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE tournament DROP rounds, DROP status');
        $this->addSql('ALTER TABLE tournament_game DROP FOREIGN KEY FK_14A683B2CB68070C');
        $this->addSql('ALTER TABLE tournament_game DROP FOREIGN KEY FK_14A683B2D530A29C');
        $this->addSql('DROP INDEX UNIQ_14A683B2CB68070C ON tournament_game');
        $this->addSql('DROP INDEX UNIQ_14A683B2D530A29C ON tournament_game');
        $this->addSql('ALTER TABLE tournament_game DROP player_white_id, DROP player_black_id');
    }
}
