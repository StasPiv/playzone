<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160117202524 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C550C49C9');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C6700FB18');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CAC4A8C94');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CD64D8FC7');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C550C49C9 FOREIGN KEY (id_timecontrol) REFERENCES timecontrol (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C6700FB18 FOREIGN KEY (id_black) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CAC4A8C94 FOREIGN KEY (id_white) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CD64D8FC7 FOREIGN KEY (id_to_move) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_call DROP FOREIGN KEY FK_279B4312E68B87C8');
        $this->addSql('ALTER TABLE game_call DROP FOREIGN KEY FK_279B4312EAE7A0B');
        $this->addSql('ALTER TABLE game_call ADD CONSTRAINT FK_279B4312E68B87C8 FOREIGN KEY (id_call_to) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_call ADD CONSTRAINT FK_279B4312EAE7A0B FOREIGN KEY (id_call_from) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CAC4A8C94');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C6700FB18');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CD64D8FC7');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C550C49C9');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CAC4A8C94 FOREIGN KEY (id_white) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C6700FB18 FOREIGN KEY (id_black) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CD64D8FC7 FOREIGN KEY (id_to_move) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C550C49C9 FOREIGN KEY (id_timecontrol) REFERENCES timecontrol (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_call DROP FOREIGN KEY FK_279B4312EAE7A0B');
        $this->addSql('ALTER TABLE game_call DROP FOREIGN KEY FK_279B4312E68B87C8');
        $this->addSql('ALTER TABLE game_call ADD CONSTRAINT FK_279B4312EAE7A0B FOREIGN KEY (id_call_from) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_call ADD CONSTRAINT FK_279B4312E68B87C8 FOREIGN KEY (id_call_to) REFERENCES user (id) ON DELETE CASCADE');
    }
}
