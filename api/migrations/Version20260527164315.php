<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260527164315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD email VARCHAR(180) DEFAULT NULL, ADD reset_password_token VARCHAR(64) DEFAULT NULL, ADD reset_password_token_expires_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_8D93D649452C9EC5 ON user (reset_password_token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_8D93D649452C9EC5 ON user');
        $this->addSql('ALTER TABLE user DROP email, DROP reset_password_token, DROP reset_password_token_expires_at');
    }
}
