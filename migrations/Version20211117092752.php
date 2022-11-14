<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211117092752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE shop_id_id shop_id INT NOT NULL');
//        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E664D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
//        $this->addSql('CREATE INDEX IDX_23A0E664D16C4DD ON article (shop_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E664D16C4DD');
//        $this->addSql('DROP INDEX IDX_23A0E664D16C4DD ON article');
        $this->addSql('ALTER TABLE article CHANGE shop_id shop_id_id INT NOT NULL');
    }
}
