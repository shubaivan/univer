<?php

declare(strict_types=1);

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180316202207 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE question_answers_corrections');
        $this->addSql('ALTER TABLE question_answers ADD question_corrections_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_answers ADD CONSTRAINT FK_5E0C131BD5A53E04 FOREIGN KEY (question_corrections_id) REFERENCES question_corrections (id)');
        $this->addSql('CREATE INDEX IDX_5E0C131BD5A53E04 ON question_answers (question_corrections_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE question_answers_corrections (id INT AUTO_INCREMENT NOT NULL, question_corrections_id INT DEFAULT NULL, answer TEXT NOT NULL COLLATE utf8_unicode_ci, is_true TINYINT(1) NOT NULL, point_eng CHAR(10) DEFAULT NULL COLLATE utf8_unicode_ci, point_heb CHAR(10) DEFAULT NULL COLLATE utf8_unicode_ci, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_6A994AB8D5A53E04 (question_corrections_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question_answers_corrections ADD CONSTRAINT FK_6A994AB8D5A53E04 FOREIGN KEY (question_corrections_id) REFERENCES question_corrections (id)');
        $this->addSql('ALTER TABLE question_answers DROP FOREIGN KEY FK_5E0C131BD5A53E04');
        $this->addSql('DROP INDEX IDX_5E0C131BD5A53E04 ON question_answers');
        $this->addSql('ALTER TABLE question_answers DROP question_corrections_id');
    }
}
