<?php

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180206115307 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE courses_courses_of_study (courses_id INT NOT NULL, courses_of_study_id INT NOT NULL, INDEX IDX_E8FB3AC8F9295384 (courses_id), INDEX IDX_E8FB3AC8AD98CD0F (courses_of_study_id), PRIMARY KEY(courses_id, courses_of_study_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE courses_courses_of_study ADD CONSTRAINT FK_E8FB3AC8F9295384 FOREIGN KEY (courses_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE courses_courses_of_study ADD CONSTRAINT FK_E8FB3AC8AD98CD0F FOREIGN KEY (courses_of_study_id) REFERENCES courses_of_study (id)');
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4CAD98CD0F');
        $this->addSql('DROP INDEX IDX_A9A55A4CAD98CD0F ON courses');
        $this->addSql('ALTER TABLE courses DROP courses_of_study_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE courses_courses_of_study');
        $this->addSql('ALTER TABLE courses ADD courses_of_study_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4CAD98CD0F FOREIGN KEY (courses_of_study_id) REFERENCES courses_of_study (id)');
        $this->addSql('CREATE INDEX IDX_A9A55A4CAD98CD0F ON courses (courses_of_study_id)');
    }
}
