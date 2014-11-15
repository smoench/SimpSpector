<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141111222937 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE commit (id INT AUTO_INCREMENT NOT NULL, merge_request_id INT DEFAULT NULL, revision VARCHAR(255) NOT NULL, result LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_4ED42EAD2FCB3624 (merge_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commit ADD CONSTRAINT FK_4ED42EAD2FCB3624 FOREIGN KEY (merge_request_id) REFERENCES merge_request (id)');
        $this->addSql('DROP TABLE push');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE push (id INT AUTO_INCREMENT NOT NULL, merge_request_id INT DEFAULT NULL, revision VARCHAR(255) NOT NULL, result LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_5F3A16642FCB3624 (merge_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE push ADD CONSTRAINT FK_5F3A16642FCB3624 FOREIGN KEY (merge_request_id) REFERENCES merge_request (id)');
        $this->addSql('DROP TABLE commit');
    }
}
