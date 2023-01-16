<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230107133123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users, parts, manufacturers, images';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE image (id UUID NOT NULL, part_id UUID DEFAULT NULL, local_path VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C53D045F4CE34BEC ON image (part_id)');
        $this->addSql('COMMENT ON COLUMN image.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN image.part_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE manufacturer (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE part (id UUID NOT NULL, part_name_id INT DEFAULT NULL, manufacturer_id INT DEFAULT NULL, part_number VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_490F70C6F14B6BDD ON part (part_name_id)');
        $this->addSql('CREATE INDEX IDX_490F70C6A23B42D ON part (manufacturer_id)');
        $this->addSql('COMMENT ON COLUMN part.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE part_name (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSONB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX email ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE part ADD CONSTRAINT FK_490F70C6F14B6BDD FOREIGN KEY (part_name_id) REFERENCES part_name (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE part ADD CONSTRAINT FK_490F70C6A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE image DROP CONSTRAINT FK_C53D045F4CE34BEC');
        $this->addSql('ALTER TABLE part DROP CONSTRAINT FK_490F70C6F14B6BDD');
        $this->addSql('ALTER TABLE part DROP CONSTRAINT FK_490F70C6A23B42D');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP TABLE part');
        $this->addSql('DROP TABLE part_name');
        $this->addSql('DROP TABLE "user"');
    }
}
