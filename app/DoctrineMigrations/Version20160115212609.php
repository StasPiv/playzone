<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160115212609 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE game_call (id INT AUTO_INCREMENT NOT NULL, id_call_from INT NOT NULL, id_call_to INT NOT NULL, id_game INT DEFAULT NULL, INDEX IDX_279B4312EAE7A0B (id_call_from), INDEX IDX_279B4312E68B87C8 (id_call_to), INDEX IDX_279B4312A80B2D8E (id_game), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_call ADD CONSTRAINT FK_279B4312EAE7A0B FOREIGN KEY (id_call_from) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_call ADD CONSTRAINT FK_279B4312E68B87C8 FOREIGN KEY (id_call_to) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_call ADD CONSTRAINT FK_279B4312A80B2D8E FOREIGN KEY (id_game) REFERENCES game (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE game_call');
    }
}
