<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200601201344 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_department (email_id INT NOT NULL, department_id INT NOT NULL, INDEX IDX_61AE1355A832C1C9 (email_id), INDEX IDX_61AE1355AE80F5DF (department_id), PRIMARY KEY(email_id, department_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_389B7835E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE preview_image (id INT AUTO_INCREMENT NOT NULL, article_id INT NOT NULL, media_object_id INT NOT NULL, UNIQUE INDEX UNIQ_18E6D42D7294869C (article_id), UNIQUE INDEX UNIQ_18E6D42D64DE5A5 (media_object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_CD1DE18A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_object (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_14D431327294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE appeal (id INT AUTO_INCREMENT NOT NULL, media_object_id INT DEFAULT NULL, department_id INT DEFAULT NULL, address VARCHAR(255) NOT NULL, phone VARCHAR(32) NOT NULL, person_type VARCHAR(255) NOT NULL, organization VARCHAR(255) NOT NULL, birth_year INT NOT NULL, full_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATE NOT NULL, UNIQUE INDEX UNIQ_9679435164DE5A5 (media_object_id), INDEX IDX_96794351AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(1024) NOT NULL, text LONGTEXT NOT NULL, preview_text LONGTEXT NOT NULL, published_at DATETIME NOT NULL, created_at DATETIME NOT NULL, slug VARCHAR(1024) NOT NULL, UNIQUE INDEX UNIQ_23A0E66989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_tag (article_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_919694F97294869C (article_id), INDEX IDX_919694F9BAD26311 (tag_id), PRIMARY KEY(article_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_department ADD CONSTRAINT FK_61AE1355A832C1C9 FOREIGN KEY (email_id) REFERENCES email (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE email_department ADD CONSTRAINT FK_61AE1355AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE preview_image ADD CONSTRAINT FK_18E6D42D7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE preview_image ADD CONSTRAINT FK_18E6D42D64DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id)');
        $this->addSql('ALTER TABLE media_object ADD CONSTRAINT FK_14D431327294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appeal ADD CONSTRAINT FK_9679435164DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id)');
        $this->addSql('ALTER TABLE appeal ADD CONSTRAINT FK_96794351AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE article_tag ADD CONSTRAINT FK_919694F97294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_tag ADD CONSTRAINT FK_919694F9BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email_department DROP FOREIGN KEY FK_61AE1355A832C1C9');
        $this->addSql('ALTER TABLE article_tag DROP FOREIGN KEY FK_919694F9BAD26311');
        $this->addSql('ALTER TABLE email_department DROP FOREIGN KEY FK_61AE1355AE80F5DF');
        $this->addSql('ALTER TABLE appeal DROP FOREIGN KEY FK_96794351AE80F5DF');
        $this->addSql('ALTER TABLE preview_image DROP FOREIGN KEY FK_18E6D42D64DE5A5');
        $this->addSql('ALTER TABLE appeal DROP FOREIGN KEY FK_9679435164DE5A5');
        $this->addSql('ALTER TABLE preview_image DROP FOREIGN KEY FK_18E6D42D7294869C');
        $this->addSql('ALTER TABLE media_object DROP FOREIGN KEY FK_14D431327294869C');
        $this->addSql('ALTER TABLE article_tag DROP FOREIGN KEY FK_919694F97294869C');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE email_department');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE preview_image');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('DROP TABLE appeal');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE article_tag');
    }
}
