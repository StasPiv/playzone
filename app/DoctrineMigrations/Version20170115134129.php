<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170115134129 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE user ADD last_tournament_id INT DEFAULT NULL, DROP tournament_id');
        $this->addSql(
            'ALTER TABLE user ADD CONSTRAINT FK_8D93D6495FDEC070 FOREIGN KEY (last_tournament_id) REFERENCES tournament (id)'
        );
        $this->addSql('CREATE INDEX IDX_8D93D6495FDEC070 ON user (last_tournament_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6495FDEC070');
        $this->addSql('DROP INDEX IDX_8D93D6495FDEC070 ON user');
        $this->addSql('ALTER TABLE user ADD tournament_id INT NOT NULL, DROP last_tournament_id');
    }
}
