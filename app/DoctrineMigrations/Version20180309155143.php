<?php declare(strict_types = 1);

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180309155143 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE question_corrections (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, user_id INT DEFAULT NULL, semesters_id INT DEFAULT NULL, exam_periods_id INT DEFAULT NULL, sub_courses_id INT DEFAULT NULL, lectors_id INT DEFAULT NULL, custom_id TINYTEXT DEFAULT NULL, year INT DEFAULT NULL, type VARCHAR(255) NOT NULL, question_number INT DEFAULT NULL, image_url CHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_3D12B6991E27F6BF (question_id), INDEX IDX_3D12B699A76ED395 (user_id), INDEX IDX_3D12B6993A36B867 (semesters_id), INDEX IDX_3D12B69994FEB3D9 (exam_periods_id), INDEX IDX_3D12B699D5E2C049 (sub_courses_id), INDEX IDX_3D12B699E2DC0673 (lectors_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question_corrections ADD CONSTRAINT FK_3D12B6991E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE question_corrections ADD CONSTRAINT FK_3D12B699A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE question_corrections ADD CONSTRAINT FK_3D12B6993A36B867 FOREIGN KEY (semesters_id) REFERENCES semesters (id)');
        $this->addSql('ALTER TABLE question_corrections ADD CONSTRAINT FK_3D12B69994FEB3D9 FOREIGN KEY (exam_periods_id) REFERENCES exam_periods (id)');
        $this->addSql('ALTER TABLE question_corrections ADD CONSTRAINT FK_3D12B699D5E2C049 FOREIGN KEY (sub_courses_id) REFERENCES sub_cources (id)');
        $this->addSql('ALTER TABLE question_corrections ADD CONSTRAINT FK_3D12B699E2DC0673 FOREIGN KEY (lectors_id) REFERENCES lectors (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE question_corrections');
    }
}
