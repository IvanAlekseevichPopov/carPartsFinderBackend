<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230304161937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE session (sess_id VARCHAR(128) NOT NULL PRIMARY KEY,sess_data BYTEA NOT NULL,sess_lifetime INTEGER NOT NULL,sess_time INTEGER NOT NULL);");
        $this->addSql("CREATE INDEX session_sess_lifetime_idx ON session (sess_lifetime);");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP table session cascade;");
    }
}
