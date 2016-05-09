<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160503180252 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE game_chat_messages (game_id INT NOT NULL, chat_message_id INT NOT NULL, INDEX IDX_45C455CDE48FD905 (game_id), UNIQUE INDEX UNIQ_45C455CD948B568F (chat_message_id), PRIMARY KEY(game_id, chat_message_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_chat_messages ADD CONSTRAINT FK_45C455CDE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_chat_messages ADD CONSTRAINT FK_45C455CD948B568F FOREIGN KEY (chat_message_id) REFERENCES chat_message (id)');
        $this->addSql('ALTER TABLE chat_message ADD time DATETIME NOT NULL, ADD type ENUM(\'game\', \'common\')');
        $this->addSql('ALTER TABLE game DROP chat_messages');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE game_chat_messages');
        $this->addSql('ALTER TABLE chat_message DROP time, DROP type');
        $this->addSql('ALTER TABLE game ADD chat_messages LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}
