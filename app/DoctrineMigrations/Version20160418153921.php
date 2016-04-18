<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160418153921 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM user_setting');

        $this->addSql('
        INSERT INTO user_setting (name, type, sort) VALUES (\'Sound move\', \'checkbox\', 10);
INSERT INTO user_setting (name, type, sort) VALUES (\'Sound draw\', \'checkbox\', 20);
INSERT INTO user_setting (name, type, sort) VALUES (\'Sound win\', \'checkbox\', 30);
INSERT INTO user_setting (name, type, sort) VALUES (\'Sound call\', \'checkbox\', 40);
INSERT INTO user_setting (name, type, sort) VALUES (\'Sound new game\', \'checkbox\', 50);
INSERT INTO user_setting (name, type, sort) VALUES (\'Draggable disabled\', \'checkbox\', 70);
INSERT INTO user_setting (name, type, sort) VALUES (\'Piece type\', \'select:merida,leipzig,case,condal,kingdom,maya,wikipedia\', 80);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM user_setting');

        $this->addSql('INSERT INTO user_setting (name, type) VALUES (\'Sound move\', \'checkbox\');');

        $this->addSql('INSERT INTO user_setting (name, type) VALUES (\'Sound draw\', \'checkbox\');');

        $this->addSql('INSERT INTO user_setting (name, type) VALUES (\'Sound win\', \'checkbox\');');

        $this->addSql('INSERT INTO user_setting (name, type) VALUES (\'Sound call\', \'checkbox\');');

        $this->addSql('INSERT INTO user_setting (name, type) VALUES (\'Sound new game\', \'checkbox\');');
    }
}
