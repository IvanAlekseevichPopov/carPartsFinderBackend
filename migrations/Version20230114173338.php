<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230114173338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP CONSTRAINT fk_c53d045f4ce34bec');
        $this->addSql('DROP INDEX idx_c53d045f4ce34bec');
        $this->addSql('ALTER TABLE image DROP part_id');
        $this->addSql('ALTER TABLE image DROP local_path');
        $this->addSql('ALTER TABLE image DROP rating');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE image ADD part_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD local_path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE image ADD rating SMALLINT NOT NULL');
        $this->addSql('COMMENT ON COLUMN image.part_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT fk_c53d045f4ce34bec FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_c53d045f4ce34bec ON image (part_id)');
    }
}
