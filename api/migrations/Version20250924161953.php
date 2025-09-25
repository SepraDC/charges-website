<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250924161953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY `FK_556BA43491A77836`');
        $this->addSql('ALTER TABLE charge ADD day_of_withdrawal INT DEFAULT NULL, DROP date');
        $this->addSql('ALTER TABLE charge ADD CONSTRAINT FK_556BA43491A77836 FOREIGN KEY (charge_type_id) REFERENCES charge_type (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA43491A77836');
        $this->addSql('ALTER TABLE charge ADD date DATE DEFAULT NULL, DROP day_of_withdrawal');
        $this->addSql('ALTER TABLE charge ADD CONSTRAINT `FK_556BA43491A77836` FOREIGN KEY (charge_type_id) REFERENCES charge_type (id)');
    }
}
