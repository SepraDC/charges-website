<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210717172144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bank (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE charge (id INT AUTO_INCREMENT NOT NULL, bank_id INT NOT NULL, charge_type_id INT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, amount NUMERIC(5, 2) NOT NULL, state TINYINT(1) NOT NULL, date DATE DEFAULT NULL, INDEX IDX_556BA43411C8FB41 (bank_id), INDEX IDX_556BA43491A77836 (charge_type_id), INDEX IDX_556BA434A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE charge_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE charge ADD CONSTRAINT FK_556BA43411C8FB41 FOREIGN KEY (bank_id) REFERENCES bank (id)');
        $this->addSql('ALTER TABLE charge ADD CONSTRAINT FK_556BA43491A77836 FOREIGN KEY (charge_type_id) REFERENCES charge_type (id)');
        $this->addSql('ALTER TABLE charge ADD CONSTRAINT FK_556BA434A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA43411C8FB41');
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA43491A77836');
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA434A76ED395');
        $this->addSql('DROP TABLE bank');
        $this->addSql('DROP TABLE charge');
        $this->addSql('DROP TABLE charge_type');
        $this->addSql('DROP TABLE user');
    }
}
