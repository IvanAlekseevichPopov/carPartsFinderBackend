<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230116061703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'added images table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE file (id UUID NOT NULL, uploaded_by_id UUID DEFAULT NULL, part_id UUID DEFAULT NULL, document_type INT NOT NULL, rating INT DEFAULT NULL, check_sum VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8C9F3610A2B28FE8 ON file (uploaded_by_id)');
        $this->addSql('CREATE INDEX IDX_8C9F36104CE34BEC ON file (part_id)');
        $this->addSql('COMMENT ON COLUMN file.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN file.uploaded_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN file.part_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610A2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36104CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE image DROP CONSTRAINT fk_c53d045f4ce34bec');
        $this->addSql('DROP TABLE image');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE image (id UUID NOT NULL, part_id UUID DEFAULT NULL, local_path VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_c53d045f4ce34bec ON image (part_id)');
        $this->addSql('COMMENT ON COLUMN image.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN image.part_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT fk_c53d045f4ce34bec FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT FK_8C9F3610A2B28FE8');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT FK_8C9F36104CE34BEC');
        $this->addSql('DROP TABLE file');
    }
}
