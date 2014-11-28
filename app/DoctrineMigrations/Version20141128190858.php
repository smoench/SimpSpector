<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141128190858 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE project ADD name VARCHAR(255) NOT NULL, ADD create_at DATETIME NOT NULL, ADD update_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE commit ADD create_at DATETIME NOT NULL, ADD update_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE merge_request ADD name VARCHAR(255) NOT NULL, ADD status VARCHAR(255) NOT NULL, ADD create_at DATETIME NOT NULL, ADD update_at DATETIME NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE commit DROP create_at, DROP update_at');
        $this->addSql('ALTER TABLE merge_request DROP name, DROP status, DROP create_at, DROP update_at');
        $this->addSql('ALTER TABLE project DROP name, DROP create_at, DROP update_at');
    }
}
