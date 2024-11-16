<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241116150427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders ADD payment_method VARCHAR(50) NOT NULL, ADD shipping_status VARCHAR(50) NOT NULL, ADD payment_status VARCHAR(50) NOT NULL, DROP status, CHANGE shipping_fee shipping_fee NUMERIC(10, 2) DEFAULT 0 NOT NULL, CHANGE discount discount NUMERIC(10, 2) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders ADD status VARCHAR(255) NOT NULL, DROP payment_method, DROP shipping_status, DROP payment_status, CHANGE shipping_fee shipping_fee NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, CHANGE discount discount NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL');
    }
}
