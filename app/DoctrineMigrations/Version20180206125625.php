<?php

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180206125625 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sub_courses_courses (sub_courses_id INT NOT NULL, courses_id INT NOT NULL, INDEX IDX_A27D7FFD5E2C049 (sub_courses_id), INDEX IDX_A27D7FFF9295384 (courses_id), PRIMARY KEY(sub_courses_id, courses_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_courses_courses ADD CONSTRAINT FK_A27D7FFD5E2C049 FOREIGN KEY (sub_courses_id) REFERENCES sub_cources (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sub_courses_courses ADD CONSTRAINT FK_A27D7FFF9295384 FOREIGN KEY (courses_id) REFERENCES courses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sub_cources DROP FOREIGN KEY FK_CA5BA5C8F9295384');
        $this->addSql('DROP INDEX IDX_CA5BA5C8F9295384 ON sub_cources');
        $this->addSql('ALTER TABLE sub_cources DROP courses_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sub_courses_courses');
        $this->addSql('ALTER TABLE sub_cources ADD courses_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_cources ADD CONSTRAINT FK_CA5BA5C8F9295384 FOREIGN KEY (courses_id) REFERENCES courses (id)');
        $this->addSql('CREATE INDEX IDX_CA5BA5C8F9295384 ON sub_cources (courses_id)');
    }
}
