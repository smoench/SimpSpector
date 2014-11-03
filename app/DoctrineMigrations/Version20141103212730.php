<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141103212730 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, remoteId VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE merge_request (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, remoteId VARCHAR(255) NOT NULL, sourceBranch VARCHAR(255) NOT NULL, INDEX IDX_5F2666E1166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE push (id INT AUTO_INCREMENT NOT NULL, merge_request_id INT DEFAULT NULL, revision VARCHAR(255) NOT NULL, result LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_5F3A16642FCB3624 (merge_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE merge_request ADD CONSTRAINT FK_5F2666E1166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE push ADD CONSTRAINT FK_5F3A16642FCB3624 FOREIGN KEY (merge_request_id) REFERENCES merge_request (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE merge_request DROP FOREIGN KEY FK_5F2666E1166D1F9C');
        $this->addSql('ALTER TABLE push DROP FOREIGN KEY FK_5F3A16642FCB3624');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE merge_request');
        $this->addSql('DROP TABLE push');
    }
}
