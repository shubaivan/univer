<?php declare(strict_types = 1);

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180312181227 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question_corrections ADD courses_of_study_id INT DEFAULT NULL, ADD courses_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_corrections ADD CONSTRAINT FK_3D12B699AD98CD0F FOREIGN KEY (courses_of_study_id) REFERENCES courses_of_study (id)');
        $this->addSql('ALTER TABLE question_corrections ADD CONSTRAINT FK_3D12B699F9295384 FOREIGN KEY (courses_id) REFERENCES courses (id)');
        $this->addSql('CREATE INDEX IDX_3D12B699AD98CD0F ON question_corrections (courses_of_study_id)');
        $this->addSql('CREATE INDEX IDX_3D12B699F9295384 ON question_corrections (courses_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question_corrections DROP FOREIGN KEY FK_3D12B699AD98CD0F');
        $this->addSql('ALTER TABLE question_corrections DROP FOREIGN KEY FK_3D12B699F9295384');
        $this->addSql('DROP INDEX IDX_3D12B699AD98CD0F ON question_corrections');
        $this->addSql('DROP INDEX IDX_3D12B699F9295384 ON question_corrections');
        $this->addSql('ALTER TABLE question_corrections DROP courses_of_study_id, DROP courses_id');
    }
}
