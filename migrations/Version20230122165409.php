<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230122165409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE part DROP CONSTRAINT fk_490f70c6a23b42d');
        $this->addSql('DROP SEQUENCE manufacturer_id_seq CASCADE');
        $this->addSql('CREATE TABLE brand (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE car_model (id SERIAL NOT NULL, brand_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_83EF70E44F5D008 ON car_model (brand_id)');
        $this->addSql('ALTER TABLE car_model ADD CONSTRAINT FK_83EF70E44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE manufacturer');
        $this->addSql('DROP INDEX idx_490f70c6a23b42d');
        $this->addSql('ALTER TABLE part RENAME COLUMN manufacturer_id TO brand_id');
        $this->addSql('ALTER TABLE part ADD CONSTRAINT FK_490F70C644F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_490F70C644F5D008 ON part (brand_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE part DROP CONSTRAINT FK_490F70C644F5D008');
        $this->addSql('CREATE SEQUENCE manufacturer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE manufacturer (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE car_model DROP CONSTRAINT FK_83EF70E44F5D008');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE car_model');
        $this->addSql('DROP INDEX IDX_490F70C644F5D008');
        $this->addSql('ALTER TABLE part RENAME COLUMN brand_id TO manufacturer_id');
        $this->addSql('ALTER TABLE part ADD CONSTRAINT fk_490f70c6a23b42d FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_490f70c6a23b42d ON part (manufacturer_id)');
    }
}
