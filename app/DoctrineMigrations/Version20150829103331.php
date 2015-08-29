<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150829103331 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE branch (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_BB861B1F166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, repository_url VARCHAR(255) NOT NULL, web_url VARCHAR(255) NOT NULL, remoteId VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, commit_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_389B783166D1F9C (project_id), INDEX IDX_389B7833D5814AC (commit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commit (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, revision VARCHAR(255) NOT NULL, git_repository VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, result_issues LONGBLOB DEFAULT NULL, result_metrics LONGBLOB DEFAULT NULL, INDEX IDX_4ED42EAD166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commit_merge_request (commit_id INT NOT NULL, merge_request_id INT NOT NULL, INDEX IDX_9B87BA913D5814AC (commit_id), INDEX IDX_9B87BA912FCB3624 (merge_request_id), PRIMARY KEY(commit_id, merge_request_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commit_branch (commit_id INT NOT NULL, branch_id INT NOT NULL, INDEX IDX_5ED1DCE33D5814AC (commit_id), INDEX IDX_5ED1DCE3DCD6CC49 (branch_id), PRIMARY KEY(commit_id, branch_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE merge_request (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, remote_id VARCHAR(255) NOT NULL, source_branch VARCHAR(255) NOT NULL, target_branch VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5F2666E1166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE branch ADD CONSTRAINT FK_BB861B1F166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B7833D5814AC FOREIGN KEY (commit_id) REFERENCES commit (id)');
        $this->addSql('ALTER TABLE commit ADD CONSTRAINT FK_4ED42EAD166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE commit_merge_request ADD CONSTRAINT FK_9B87BA913D5814AC FOREIGN KEY (commit_id) REFERENCES commit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commit_merge_request ADD CONSTRAINT FK_9B87BA912FCB3624 FOREIGN KEY (merge_request_id) REFERENCES merge_request (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commit_branch ADD CONSTRAINT FK_5ED1DCE33D5814AC FOREIGN KEY (commit_id) REFERENCES commit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commit_branch ADD CONSTRAINT FK_5ED1DCE3DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE merge_request ADD CONSTRAINT FK_5F2666E1166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commit_branch DROP FOREIGN KEY FK_5ED1DCE3DCD6CC49');
        $this->addSql('ALTER TABLE branch DROP FOREIGN KEY FK_BB861B1F166D1F9C');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783166D1F9C');
        $this->addSql('ALTER TABLE commit DROP FOREIGN KEY FK_4ED42EAD166D1F9C');
        $this->addSql('ALTER TABLE merge_request DROP FOREIGN KEY FK_5F2666E1166D1F9C');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B7833D5814AC');
        $this->addSql('ALTER TABLE commit_merge_request DROP FOREIGN KEY FK_9B87BA913D5814AC');
        $this->addSql('ALTER TABLE commit_branch DROP FOREIGN KEY FK_5ED1DCE33D5814AC');
        $this->addSql('ALTER TABLE commit_merge_request DROP FOREIGN KEY FK_9B87BA912FCB3624');
        $this->addSql('DROP TABLE branch');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE commit');
        $this->addSql('DROP TABLE commit_merge_request');
        $this->addSql('DROP TABLE commit_branch');
        $this->addSql('DROP TABLE merge_request');
    }
}
