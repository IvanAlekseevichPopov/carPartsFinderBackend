<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230404095414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added version column to part table for optimistic locking';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE part ADD version INT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE part DROP version');
    }
}
