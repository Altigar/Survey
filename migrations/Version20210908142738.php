<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210908142738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE external_person_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE external_person (id INT NOT NULL, ip VARCHAR(40) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE answer DROP CONSTRAINT fk_dadd4a25217bbb47');
        $this->addSql('DROP INDEX idx_dadd4a25217bbb47');
        $this->addSql('ALTER TABLE answer DROP person_id');
        $this->addSql('ALTER TABLE pass ADD external_person_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pass ALTER person_id DROP NOT NULL');
        $this->addSql('ALTER TABLE pass ADD CONSTRAINT FK_CE70D4245201453 FOREIGN KEY (external_person_id) REFERENCES external_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_CE70D4245201453 ON pass (external_person_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pass DROP CONSTRAINT FK_CE70D4245201453');
        $this->addSql('DROP SEQUENCE external_person_id_seq CASCADE');
        $this->addSql('DROP TABLE external_person');
        $this->addSql('ALTER TABLE answer ADD person_id INT NOT NULL');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT fk_dadd4a25217bbb47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_dadd4a25217bbb47 ON answer (person_id)');
        $this->addSql('DROP INDEX IDX_CE70D4245201453');
        $this->addSql('ALTER TABLE pass DROP external_person_id');
        $this->addSql('ALTER TABLE pass ALTER person_id SET NOT NULL');
    }
}
