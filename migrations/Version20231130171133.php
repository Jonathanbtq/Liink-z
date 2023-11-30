<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130171133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE social_link ADD snapchat VARCHAR(255) DEFAULT NULL, ADD instagram VARCHAR(255) DEFAULT NULL, ADD youtube VARCHAR(255) DEFAULT NULL, ADD twitch VARCHAR(255) DEFAULT NULL, ADD email VARCHAR(255) DEFAULT NULL, ADD facebook VARCHAR(255) DEFAULT NULL, ADD twitter VARCHAR(255) DEFAULT NULL, ADD tiktok VARCHAR(255) DEFAULT NULL, ADD whatsapp VARCHAR(255) DEFAULT NULL, ADD amazon VARCHAR(255) DEFAULT NULL, ADD applemusic VARCHAR(255) DEFAULT NULL, ADD discord VARCHAR(255) DEFAULT NULL, ADD github VARCHAR(255) DEFAULT NULL, ADD kick VARCHAR(255) DEFAULT NULL, ADD etsy VARCHAR(255) NOT NULL, ADD linkedin VARCHAR(255) DEFAULT NULL, ADD patreon VARCHAR(255) DEFAULT NULL, ADD printerest VARCHAR(255) DEFAULT NULL, ADD spotify VARCHAR(255) DEFAULT NULL, ADD telegram VARCHAR(255) DEFAULT NULL, DROP social_name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE social_link ADD social_name VARCHAR(50) NOT NULL, DROP snapchat, DROP instagram, DROP youtube, DROP twitch, DROP email, DROP facebook, DROP twitter, DROP tiktok, DROP whatsapp, DROP amazon, DROP applemusic, DROP discord, DROP github, DROP kick, DROP etsy, DROP linkedin, DROP patreon, DROP printerest, DROP spotify, DROP telegram');
    }
}
