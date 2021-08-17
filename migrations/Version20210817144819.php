<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210817144819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE option DROP scale');
        $this->addSql('ALTER TABLE option DROP scale_from_text');
        $this->addSql('ALTER TABLE option DROP scale_to_text');
        $this->addSql('ALTER TABLE option DROP "row"');
        $this->addSql('ALTER TABLE question ADD scale SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE question ADD scale_from_text VARCHAR(40) DEFAULT NULL');
        $this->addSql('ALTER TABLE question ADD scale_to_text VARCHAR(40) DEFAULT NULL');
        $this->addSql('ALTER TABLE question ADD row SMALLINT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE option ADD scale SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE option ADD scale_from_text VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE option ADD scale_to_text VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE option ADD "row" SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE question DROP scale');
        $this->addSql('ALTER TABLE question DROP scale_from_text');
        $this->addSql('ALTER TABLE question DROP scale_to_text');
        $this->addSql('ALTER TABLE question DROP row');
    }
}
