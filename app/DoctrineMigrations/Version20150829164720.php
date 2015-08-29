<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150829164720 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE news_stream_item ADD project_id INT DEFAULT NULL, ADD commit_id INT DEFAULT NULL, ADD merge_request_id INT DEFAULT NULL, ADD branch_id INT DEFAULT NULL, DROP attributes');
        $this->addSql('ALTER TABLE news_stream_item ADD CONSTRAINT FK_8139AAF2166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE news_stream_item ADD CONSTRAINT FK_8139AAF23D5814AC FOREIGN KEY (commit_id) REFERENCES commit (id)');
        $this->addSql('ALTER TABLE news_stream_item ADD CONSTRAINT FK_8139AAF22FCB3624 FOREIGN KEY (merge_request_id) REFERENCES merge_request (id)');
        $this->addSql('ALTER TABLE news_stream_item ADD CONSTRAINT FK_8139AAF2DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('CREATE INDEX IDX_8139AAF2166D1F9C ON news_stream_item (project_id)');
        $this->addSql('CREATE INDEX IDX_8139AAF23D5814AC ON news_stream_item (commit_id)');
        $this->addSql('CREATE INDEX IDX_8139AAF22FCB3624 ON news_stream_item (merge_request_id)');
        $this->addSql('CREATE INDEX IDX_8139AAF2DCD6CC49 ON news_stream_item (branch_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE news_stream_item DROP FOREIGN KEY FK_8139AAF2166D1F9C');
        $this->addSql('ALTER TABLE news_stream_item DROP FOREIGN KEY FK_8139AAF23D5814AC');
        $this->addSql('ALTER TABLE news_stream_item DROP FOREIGN KEY FK_8139AAF22FCB3624');
        $this->addSql('ALTER TABLE news_stream_item DROP FOREIGN KEY FK_8139AAF2DCD6CC49');
        $this->addSql('DROP INDEX IDX_8139AAF2166D1F9C ON news_stream_item');
        $this->addSql('DROP INDEX IDX_8139AAF23D5814AC ON news_stream_item');
        $this->addSql('DROP INDEX IDX_8139AAF22FCB3624 ON news_stream_item');
        $this->addSql('DROP INDEX IDX_8139AAF2DCD6CC49 ON news_stream_item');
        $this->addSql('ALTER TABLE news_stream_item ADD attributes LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', DROP project_id, DROP commit_id, DROP merge_request_id, DROP branch_id');
    }
}
