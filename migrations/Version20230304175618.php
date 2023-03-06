<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230304175618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE cache_items( item_id       varchar(255) not null constraint cache_items_pkey primary key, item_data     bytea        not null, item_lifetime integer, item_time     integer      not null);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop table cache_items cascade;');
    }
}
