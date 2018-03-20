<?php declare(strict_types = 1);

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180320115234 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users ADD courses_of_study_id INT DEFAULT NULL, DROP courses_of_study');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9AD98CD0F FOREIGN KEY (courses_of_study_id) REFERENCES courses_of_study (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9AD98CD0F ON users (courses_of_study_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9AD98CD0F');
        $this->addSql('DROP INDEX IDX_1483A5E9AD98CD0F ON users');
        $this->addSql('ALTER TABLE users ADD courses_of_study VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP courses_of_study_id');
    }
}
