<?php

declare(strict_types=1);

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180307144841 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, courses_of_study_id INT DEFAULT NULL, user_id INT DEFAULT NULL, admin_id INT DEFAULT NULL, text TEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, count INT NOT NULL, page INT NOT NULL, sort_by VARCHAR(255) NOT NULL, sort_order VARCHAR(255) NOT NULL, years VARCHAR(255) DEFAULT NULL, search VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_5387574AAD98CD0F (courses_of_study_id), INDEX IDX_5387574AA76ED395 (user_id), INDEX IDX_5387574A642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events_courses (events_id INT NOT NULL, courses_id INT NOT NULL, INDEX IDX_FFC423569D6A1065 (events_id), INDEX IDX_FFC42356F9295384 (courses_id), PRIMARY KEY(events_id, courses_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events_sub_courses (events_id INT NOT NULL, sub_courses_id INT NOT NULL, INDEX IDX_4C174CBB9D6A1065 (events_id), INDEX IDX_4C174CBBD5E2C049 (sub_courses_id), PRIMARY KEY(events_id, sub_courses_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events_lectors (events_id INT NOT NULL, lectors_id INT NOT NULL, INDEX IDX_726393A59D6A1065 (events_id), INDEX IDX_726393A5E2DC0673 (lectors_id), PRIMARY KEY(events_id, lectors_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events_exam_periods (events_id INT NOT NULL, exam_periods_id INT NOT NULL, INDEX IDX_F322FA3A9D6A1065 (events_id), INDEX IDX_F322FA3A94FEB3D9 (exam_periods_id), PRIMARY KEY(events_id, exam_periods_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events_semesters (events_id INT NOT NULL, semesters_id INT NOT NULL, INDEX IDX_5CB60C629D6A1065 (events_id), INDEX IDX_5CB60C623A36B867 (semesters_id), PRIMARY KEY(events_id, semesters_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AAD98CD0F FOREIGN KEY (courses_of_study_id) REFERENCES courses_of_study (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE events_courses ADD CONSTRAINT FK_FFC423569D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_courses ADD CONSTRAINT FK_FFC42356F9295384 FOREIGN KEY (courses_id) REFERENCES courses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_sub_courses ADD CONSTRAINT FK_4C174CBB9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_sub_courses ADD CONSTRAINT FK_4C174CBBD5E2C049 FOREIGN KEY (sub_courses_id) REFERENCES sub_cources (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_lectors ADD CONSTRAINT FK_726393A59D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_lectors ADD CONSTRAINT FK_726393A5E2DC0673 FOREIGN KEY (lectors_id) REFERENCES lectors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_exam_periods ADD CONSTRAINT FK_F322FA3A9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_exam_periods ADD CONSTRAINT FK_F322FA3A94FEB3D9 FOREIGN KEY (exam_periods_id) REFERENCES exam_periods (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_semesters ADD CONSTRAINT FK_5CB60C629D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_semesters ADD CONSTRAINT FK_5CB60C623A36B867 FOREIGN KEY (semesters_id) REFERENCES semesters (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments CHANGE approve approve TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE events_courses DROP FOREIGN KEY FK_FFC423569D6A1065');
        $this->addSql('ALTER TABLE events_sub_courses DROP FOREIGN KEY FK_4C174CBB9D6A1065');
        $this->addSql('ALTER TABLE events_lectors DROP FOREIGN KEY FK_726393A59D6A1065');
        $this->addSql('ALTER TABLE events_exam_periods DROP FOREIGN KEY FK_F322FA3A9D6A1065');
        $this->addSql('ALTER TABLE events_semesters DROP FOREIGN KEY FK_5CB60C629D6A1065');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE events_courses');
        $this->addSql('DROP TABLE events_sub_courses');
        $this->addSql('DROP TABLE events_lectors');
        $this->addSql('DROP TABLE events_exam_periods');
        $this->addSql('DROP TABLE events_semesters');
        $this->addSql('ALTER TABLE comments CHANGE approve approve TINYINT(1) DEFAULT \'0\'');
    }
}
