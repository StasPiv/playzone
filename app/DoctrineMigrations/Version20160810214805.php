<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160810214805 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_problem (id INT AUTO_INCREMENT NOT NULL, problem_id INT DEFAULT NULL, user_id INT DEFAULT NULL, solved INT NOT NULL, total INT NOT NULL, percent DOUBLE PRECISION NOT NULL, INDEX IDX_8FEAD2C2A0DCED86 (problem_id), INDEX IDX_8FEAD2C2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_problem ADD CONSTRAINT FK_8FEAD2C2A0DCED86 FOREIGN KEY (problem_id) REFERENCES problem (id)');
        $this->addSql('ALTER TABLE user_problem ADD CONSTRAINT FK_8FEAD2C2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_problem');
    }
}
