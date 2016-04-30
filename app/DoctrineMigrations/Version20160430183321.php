<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160430183321 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("
        INSERT INTO user (login, email, password, hash, confirm, in_rest, left_rest, gone_in_rest, class, rating, win, draw, lose, lose_time, last_auth, immortal_id, another_login, last_move, balance, settings) VALUES ('Robot', 'robot@yandex.ru', 'e10adc3949ba59abbe56e057f20f883e', '098f6bcd4621d373cade4e832627b4f6', 0, 0, 23, '2016-04-29 00:00:00', 'D', 2443, 20, 10, 0, 0, '2016-04-29 00:00:00', 88, '', '2016-04-29 00:00:00', 0, 'N;');");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM user WHERE login = 'Robot'");
    }
}
