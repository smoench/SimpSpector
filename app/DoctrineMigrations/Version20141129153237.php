<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141129153237 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commit ADD project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commit ADD CONSTRAINT FK_4ED42EAD166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_4ED42EAD166D1F9C ON commit (project_id)');
        $this->addSql('UPDATE commit c JOIN merge_request m ON m.id=c.merge_request_id SET c.project_id=m.project_id;');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commit DROP FOREIGN KEY FK_4ED42EAD166D1F9C');
        $this->addSql('DROP INDEX IDX_4ED42EAD166D1F9C ON commit');
        $this->addSql('ALTER TABLE commit DROP project_id');
    }
}
