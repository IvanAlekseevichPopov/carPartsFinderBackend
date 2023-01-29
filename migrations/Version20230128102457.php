<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230128102457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car_model ADD children_parts_parsed BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE car_model ADD modifications JSONB DEFAULT NULL');
        $this->addSql('ALTER TABLE part ADD suitable_for_models JSONB NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE part DROP suitable_for_models');
        $this->addSql('ALTER TABLE car_model DROP children_parts_parsed');
        $this->addSql('ALTER TABLE car_model DROP modifications');
    }
}
