<?php

declare(strict_types=1);

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180307162503 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_question_answer_open ADD questions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_question_answer_open ADD CONSTRAINT FK_A77E6C96BCB134CE FOREIGN KEY (questions_id) REFERENCES questions (id)');
        $this->addSql('CREATE INDEX IDX_A77E6C96BCB134CE ON user_question_answer_open (questions_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_question_answer_open DROP FOREIGN KEY FK_A77E6C96BCB134CE');
        $this->addSql('DROP INDEX IDX_A77E6C96BCB134CE ON user_question_answer_open');
        $this->addSql('ALTER TABLE user_question_answer_open DROP questions_id');
    }
}
