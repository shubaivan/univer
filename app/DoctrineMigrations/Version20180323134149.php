<?php

declare(strict_types=1);

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180323134149 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE repeated_questions DROP FOREIGN KEY FK_9D9AD824BCB134CE');
        $this->addSql('DROP INDEX unique_favorites ON repeated_questions');
        $this->addSql('DROP INDEX IDX_9D9AD824BCB134CE ON repeated_questions');
        $this->addSql('ALTER TABLE repeated_questions ADD questions_repeated_id INT DEFAULT NULL, CHANGE questions_id questions_origin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE repeated_questions ADD CONSTRAINT FK_9D9AD824F06C5049 FOREIGN KEY (questions_origin_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE repeated_questions ADD CONSTRAINT FK_9D9AD824B7D8CF9F FOREIGN KEY (questions_repeated_id) REFERENCES questions (id)');
        $this->addSql('CREATE INDEX IDX_9D9AD824F06C5049 ON repeated_questions (questions_origin_id)');
        $this->addSql('CREATE INDEX IDX_9D9AD824B7D8CF9F ON repeated_questions (questions_repeated_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_repeated ON repeated_questions (questions_repeated_id, questions_origin_id, user_id)');
        $this->addSql('ALTER TABLE refresh_tokens CHANGE valid valid DATETIME NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE refresh_tokens CHANGE valid valid DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE repeated_questions DROP FOREIGN KEY FK_9D9AD824F06C5049');
        $this->addSql('ALTER TABLE repeated_questions DROP FOREIGN KEY FK_9D9AD824B7D8CF9F');
        $this->addSql('DROP INDEX IDX_9D9AD824F06C5049 ON repeated_questions');
        $this->addSql('DROP INDEX IDX_9D9AD824B7D8CF9F ON repeated_questions');
        $this->addSql('DROP INDEX unique_repeated ON repeated_questions');
        $this->addSql('ALTER TABLE repeated_questions ADD questions_id INT DEFAULT NULL, DROP questions_origin_id, DROP questions_repeated_id');
        $this->addSql('ALTER TABLE repeated_questions ADD CONSTRAINT FK_9D9AD824BCB134CE FOREIGN KEY (questions_id) REFERENCES questions (id)');
        $this->addSql('CREATE UNIQUE INDEX unique_favorites ON repeated_questions (questions_id, user_id)');
        $this->addSql('CREATE INDEX IDX_9D9AD824BCB134CE ON repeated_questions (questions_id)');
    }
}
