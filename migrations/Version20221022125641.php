<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221022125641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added service tables for messenger, sessions and cache';
    }

    public function up(Schema $schema): void
    {
        // messages table
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');

        // sessions table
        $this->addSql('CREATE TABLE session (sess_id VARCHAR(128) NOT NULL PRIMARY KEY,sess_data BYTEA NOT NULL,sess_lifetime INTEGER NOT NULL,sess_time INTEGER NOT NULL);');
        $this->addSql('CREATE INDEX session_sess_lifetime_idx ON session (sess_lifetime);');

        // cache table
        $this->addSql('CREATE TABLE cache_items( item_id       varchar(255) not null constraint cache_items_pkey primary key, item_data     bytea        not null, item_lifetime integer, item_time     integer      not null);');
    }

    public function down(Schema $schema): void
    {
        // drop messages table
        $this->addSql('DROP TABLE messenger_messages');

        // drop sessions table
        $this->addSql('DROP table session cascade;');

        // drop cache table
        $this->addSql('drop table cache_items cascade;');
    }
}
