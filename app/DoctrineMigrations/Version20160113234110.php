<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160113234110 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, id_white INT NOT NULL, id_black INT NOT NULL, id_to_move INT NOT NULL, id_timecontrol INT NOT NULL, pgn LONGTEXT NOT NULL, pgn_alt LONGTEXT NOT NULL, status LONGTEXT NOT NULL, rate TINYINT(1) NOT NULL, result_white DOUBLE PRECISION DEFAULT NULL, result_black DOUBLE PRECISION DEFAULT NULL, time_white INT NOT NULL, time_black INT NOT NULL, time_last_move TIME NOT NULL, time_over TINYINT(1) NOT NULL, gone_in_rest_white TIME DEFAULT NULL, gone_in_rest_black TIME DEFAULT NULL, INDEX IDX_232B318CAC4A8C94 (id_white), INDEX IDX_232B318C6700FB18 (id_black), INDEX IDX_232B318CD64D8FC7 (id_to_move), INDEX IDX_232B318C550C49C9 (id_timecontrol), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CAC4A8C94 FOREIGN KEY (id_white) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C6700FB18 FOREIGN KEY (id_black) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CD64D8FC7 FOREIGN KEY (id_to_move) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C550C49C9 FOREIGN KEY (id_timecontrol) REFERENCES timecontrol (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE game');
    }
}
