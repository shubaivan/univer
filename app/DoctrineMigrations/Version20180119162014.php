<?php

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180119162014 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE auestion_answers ADD questions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE auestion_answers ADD CONSTRAINT FK_B82B2D85BCB134CE FOREIGN KEY (questions_id) REFERENCES questions (id)');
        $this->addSql('CREATE INDEX IDX_B82B2D85BCB134CE ON auestion_answers (questions_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE auestion_answers DROP FOREIGN KEY FK_B82B2D85BCB134CE');
        $this->addSql('DROP INDEX IDX_B82B2D85BCB134CE ON auestion_answers');
        $this->addSql('ALTER TABLE auestion_answers DROP questions_id');
    }
}
