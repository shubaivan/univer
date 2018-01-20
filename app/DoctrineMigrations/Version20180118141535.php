<?php

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180118141535 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_question_answer_test ADD question_answers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_question_answer_test ADD CONSTRAINT FK_DB71913E1E6B8446 FOREIGN KEY (question_answers_id) REFERENCES auestion_answers (id)');
        $this->addSql('CREATE INDEX IDX_DB71913E1E6B8446 ON user_question_answer_test (question_answers_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_question_answer_test DROP FOREIGN KEY FK_DB71913E1E6B8446');
        $this->addSql('DROP INDEX IDX_DB71913E1E6B8446 ON user_question_answer_test');
        $this->addSql('ALTER TABLE user_question_answer_test DROP question_answers_id');
    }
}
