<?php declare(strict_types = 1);

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180309194818 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question_corrections DROP FOREIGN KEY FK_3D12B6991E27F6BF');
        $this->addSql('DROP INDEX IDX_3D12B6991E27F6BF ON question_corrections');
        $this->addSql('ALTER TABLE question_corrections CHANGE question_id questions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_corrections ADD CONSTRAINT FK_3D12B699BCB134CE FOREIGN KEY (questions_id) REFERENCES questions (id)');
        $this->addSql('CREATE INDEX IDX_3D12B699BCB134CE ON question_corrections (questions_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question_corrections DROP FOREIGN KEY FK_3D12B699BCB134CE');
        $this->addSql('DROP INDEX IDX_3D12B699BCB134CE ON question_corrections');
        $this->addSql('ALTER TABLE question_corrections CHANGE questions_id question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_corrections ADD CONSTRAINT FK_3D12B6991E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('CREATE INDEX IDX_3D12B6991E27F6BF ON question_corrections (question_id)');
    }
}
