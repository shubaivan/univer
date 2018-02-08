<?php declare(strict_types = 1);

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180208113340 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_question_answer_test DROP FOREIGN KEY FK_DB71913E1E6B8446');
        $this->addSql('CREATE TABLE question_answers (id INT AUTO_INCREMENT NOT NULL, questions_id INT DEFAULT NULL, answer TEXT NOT NULL, is_true TINYINT(1) NOT NULL, point_eng CHAR(10) DEFAULT NULL, point_heb CHAR(10) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_5E0C131BBCB134CE (questions_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question_answers ADD CONSTRAINT FK_5E0C131BBCB134CE FOREIGN KEY (questions_id) REFERENCES questions (id)');
        $this->addSql('DROP TABLE auestion_answers');
        $this->addSql('ALTER TABLE user_question_answer_test ADD CONSTRAINT FK_DB71913E1E6B8446 FOREIGN KEY (question_answers_id) REFERENCES question_answers (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_question_answer_test DROP FOREIGN KEY FK_DB71913E1E6B8446');
        $this->addSql('CREATE TABLE auestion_answers (id INT AUTO_INCREMENT NOT NULL, questions_id INT DEFAULT NULL, answer TEXT DEFAULT NULL COLLATE utf8_unicode_ci, is_true TINYINT(1) DEFAULT NULL, point_eng CHAR(10) NOT NULL COLLATE utf8_unicode_ci, point_heb CHAR(10) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_B82B2D85BCB134CE (questions_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auestion_answers ADD CONSTRAINT FK_B82B2D85BCB134CE FOREIGN KEY (questions_id) REFERENCES questions (id)');
        $this->addSql('DROP TABLE question_answers');
        $this->addSql('ALTER TABLE user_question_answer_test ADD CONSTRAINT FK_DB71913E1E6B8446 FOREIGN KEY (question_answers_id) REFERENCES auestion_answers (id)');
    }
}
