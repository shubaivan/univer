<?php declare(strict_types = 1);

namespace Bbt\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180312141231 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE questions ADD courses_of_study_id INT DEFAULT NULL, ADD courses_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5AD98CD0F FOREIGN KEY (courses_of_study_id) REFERENCES courses_of_study (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5F9295384 FOREIGN KEY (courses_id) REFERENCES courses (id)');
        $this->addSql('CREATE INDEX IDX_8ADC54D5AD98CD0F ON questions (courses_of_study_id)');
        $this->addSql('CREATE INDEX IDX_8ADC54D5F9295384 ON questions (courses_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5AD98CD0F');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5F9295384');
        $this->addSql('DROP INDEX IDX_8ADC54D5AD98CD0F ON questions');
        $this->addSql('DROP INDEX IDX_8ADC54D5F9295384 ON questions');
        $this->addSql('ALTER TABLE questions DROP courses_of_study_id, DROP courses_id');
    }
}
