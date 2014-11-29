<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141129104345 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE issue (id INT AUTO_INCREMENT NOT NULL, commit_id INT DEFAULT NULL, gadget VARCHAR(255) NOT NULL, level VARCHAR(255) NOT NULL, file VARCHAR(255) DEFAULT NULL, line INT DEFAULT NULL, code_snippet LONGTEXT DEFAULT NULL, extra_information LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_12AD233E3D5814AC (commit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E3D5814AC FOREIGN KEY (commit_id) REFERENCES commit (id)');
        $this->addSql('ALTER TABLE commit CHANGE result gadgets LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE issue');
        $this->addSql('ALTER TABLE commit CHANGE gadgets result LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
    }
}
