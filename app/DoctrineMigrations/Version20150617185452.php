<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150617185452 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE issue');
        $this->addSql('DROP TABLE metric');
        $this->addSql('ALTER TABLE commit ADD result_issues LONGTEXT NOT NULL, ADD result_metrics LONGTEXT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE issue (id INT AUTO_INCREMENT NOT NULL, commit_id INT DEFAULT NULL, gadget VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, level VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, file VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, line INT DEFAULT NULL, extra_information LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_12AD233E3D5814AC (commit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metric (id INT AUTO_INCREMENT NOT NULL, commit_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, value DOUBLE PRECISION NOT NULL, description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_87D62EE33D5814AC (commit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E3D5814AC FOREIGN KEY (commit_id) REFERENCES commit (id)');
        $this->addSql('ALTER TABLE metric ADD CONSTRAINT FK_87D62EE33D5814AC FOREIGN KEY (commit_id) REFERENCES commit (id)');
        $this->addSql('ALTER TABLE commit DROP result_issues, DROP result_metrics');
    }
}
