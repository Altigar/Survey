<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210507044126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE option ADD scale_from SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE option ADD scale_to SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE option ADD scale_from_text VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE option ADD scale_to_text VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE option DROP scale_from');
        $this->addSql('ALTER TABLE option DROP scale_to');
        $this->addSql('ALTER TABLE option DROP scale_from_text');
        $this->addSql('ALTER TABLE option DROP scale_to_text');
    }
}
