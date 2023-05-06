<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505222338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE links DROP INDEX UNIQ_D182A118A76ED395, ADD INDEX IDX_D182A118A76ED395 (user_id)');
        $this->addSql('ALTER TABLE links ADD CONSTRAINT FK_D182A118A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE links DROP INDEX IDX_D182A118A76ED395, ADD UNIQUE INDEX UNIQ_D182A118A76ED395 (user_id)');
        $this->addSql('ALTER TABLE links DROP FOREIGN KEY FK_D182A118A76ED395');
    }
}
