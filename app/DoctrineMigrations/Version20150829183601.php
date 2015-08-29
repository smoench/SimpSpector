<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150829183601 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE news_stream_item ADD tag_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE news_stream_item ADD CONSTRAINT FK_8139AAF2BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('CREATE INDEX IDX_8139AAF2BAD26311 ON news_stream_item (tag_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE news_stream_item DROP FOREIGN KEY FK_8139AAF2BAD26311');
        $this->addSql('DROP INDEX IDX_8139AAF2BAD26311 ON news_stream_item');
        $this->addSql('ALTER TABLE news_stream_item DROP tag_id');
    }
}
