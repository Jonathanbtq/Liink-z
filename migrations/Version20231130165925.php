<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130165925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE social_link (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, link VARCHAR(255) NOT NULL, social_name VARCHAR(50) NOT NULL, is_actived TINYINT(1) NOT NULL, INDEX IDX_79BD4A95A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE social_link ADD CONSTRAINT FK_79BD4A95A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user CHANGE color_custom color_custom VARCHAR(10) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE social_link DROP FOREIGN KEY FK_79BD4A95A76ED395');
        $this->addSql('DROP TABLE social_link');
        $this->addSql('ALTER TABLE user CHANGE color_custom color_custom VARCHAR(10) DEFAULT NULL');
    }
}
