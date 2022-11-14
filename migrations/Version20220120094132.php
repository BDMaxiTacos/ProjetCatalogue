<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220120094132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie_article DROP FOREIGN KEY FK_5DB9A0C4BCF5E72D');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE categorie_article');
        $this->addSql('ALTER TABLE article RENAME INDEX idx_23a0e66b852c405 TO IDX_23A0E664D16C4DD');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE categorie_article (categorie_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_5DB9A0C47294869C (article_id), INDEX IDX_5DB9A0C4BCF5E72D (categorie_id), PRIMARY KEY(categorie_id, article_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE categorie_article ADD CONSTRAINT FK_5DB9A0C47294869C FOREIGN KEY (article_id) REFERENCES article (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie_article ADD CONSTRAINT FK_5DB9A0C4BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article RENAME INDEX idx_23a0e664d16c4dd TO IDX_23A0E66B852C405');
    }
}
