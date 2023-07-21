<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210927085656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing fields image and abbreviation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bank ADD abbreviation VARCHAR(3) NOT NULL, ADD image_name VARCHAR(255) DEFAULT NULL, ADD image_size VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bank DROP abbreviation, DROP image_name, DROP image_size');
    }
}
