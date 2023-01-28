<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230128070802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brand (id SERIAL NOT NULL, external_id INT NOT NULL, name VARCHAR(255) NOT NULL, children_models_parsed BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1C52F9589F75D7B0 ON brand (external_id)');
        $this->addSql('CREATE TABLE car_model (id SERIAL NOT NULL, brand_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, external_id INT NOT NULL, production_start DATE NOT NULL, production_finish DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83EF70E9F75D7B0 ON car_model (external_id)');
        $this->addSql('CREATE INDEX IDX_83EF70E44F5D008 ON car_model (brand_id)');
        $this->addSql('COMMENT ON COLUMN car_model.production_start IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN car_model.production_finish IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE part (id UUID NOT NULL, part_name_id INT DEFAULT NULL, brand_id INT DEFAULT NULL, part_number VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_490F70C6F14B6BDD ON part (part_name_id)');
        $this->addSql('CREATE INDEX IDX_490F70C644F5D008 ON part (brand_id)');
        $this->addSql('COMMENT ON COLUMN part.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE part_image (id UUID NOT NULL, uploaded_by_id UUID DEFAULT NULL, part_id UUID DEFAULT NULL, check_sum VARCHAR(255) NOT NULL, rating INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_564E53BCA2B28FE8 ON part_image (uploaded_by_id)');
        $this->addSql('CREATE INDEX IDX_564E53BC4CE34BEC ON part_image (part_id)');
        $this->addSql('COMMENT ON COLUMN part_image.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_image.uploaded_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_image.part_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE part_name (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSONB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE car_model ADD CONSTRAINT FK_83EF70E44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE part ADD CONSTRAINT FK_490F70C6F14B6BDD FOREIGN KEY (part_name_id) REFERENCES part_name (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE part ADD CONSTRAINT FK_490F70C644F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE part_image ADD CONSTRAINT FK_564E53BCA2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE part_image ADD CONSTRAINT FK_564E53BC4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE car_model DROP CONSTRAINT FK_83EF70E44F5D008');
        $this->addSql('ALTER TABLE part DROP CONSTRAINT FK_490F70C6F14B6BDD');
        $this->addSql('ALTER TABLE part DROP CONSTRAINT FK_490F70C644F5D008');
        $this->addSql('ALTER TABLE part_image DROP CONSTRAINT FK_564E53BCA2B28FE8');
        $this->addSql('ALTER TABLE part_image DROP CONSTRAINT FK_564E53BC4CE34BEC');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE car_model');
        $this->addSql('DROP TABLE part');
        $this->addSql('DROP TABLE part_image');
        $this->addSql('DROP TABLE part_name');
        $this->addSql('DROP TABLE "user"');
    }
}
