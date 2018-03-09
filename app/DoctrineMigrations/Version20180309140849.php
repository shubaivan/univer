<?php declare(strict_types = 1);

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180309140849 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE repeated_questions (id INT AUTO_INCREMENT NOT NULL, questions_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_9D9AD824BCB134CE (questions_id), INDEX IDX_9D9AD824A76ED395 (user_id), UNIQUE INDEX unique_favorites (questions_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE repeated_questions ADD CONSTRAINT FK_9D9AD824BCB134CE FOREIGN KEY (questions_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE repeated_questions ADD CONSTRAINT FK_9D9AD824A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE repeated_questions');
    }
}
