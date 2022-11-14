<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220113082425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order_article` ADD id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order_article` DROP FOREIGN KEY FK_F440A72D7294869C');
        $this->addSql('ALTER TABLE `order_article` DROP FOREIGN KEY FK_F440A72D8D9F6D38');
        $this->addSql('ALTER TABLE `order_article` DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE `order_article` ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE `order_article` MODIFY id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE `order_article` CHANGE order_id order_id INT DEFAULT NULL, CHANGE article_id article_id INT DEFAULT NULL, ADD quantity INT NOT NULL');
        $this->addSql('ALTER TABLE `order_article` ADD CONSTRAINT FK_F440A72D7294869C FOREIGN KEY (article_id) REFERENCES `article` (id)');
        $this->addSql('ALTER TABLE `order_article` ADD CONSTRAINT FK_F440A72D8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');

//        $this->addSql('INSERT INTO `doctrine_migration_versions` VALUES (\'DoctrineMigrations\\\\Version20220113082425\',\'2022-01-18 14:15:27\',25)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order_article` MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE `order_article` DROP FOREIGN KEY FK_F440A72D7294869C');
        $this->addSql('ALTER TABLE `order_article` DROP FOREIGN KEY FK_F440A72D8D9F6D38');
        $this->addSql('ALTER TABLE `order_article` DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE `order_article` CHANGE article_id article_id INT NOT NULL, CHANGE order_id order_id INT NOT NULL, DROP quantity');
        $this->addSql('ALTER TABLE `order_article` ADD CONSTRAINT FK_F440A72D7294869C FOREIGN KEY (article_id) REFERENCES `article` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `order_article` ADD CONSTRAINT FK_F440A72D8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX id ON `order_article` (id)');
        $this->addSql('ALTER TABLE `order_article` ADD PRIMARY KEY (order_id, article_id)');
        $this->addSql('ALTER TABLE `order_article` DROP id');

    }
}
