<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241116082223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, quantity INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, user_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_BA388B7A76ED395 (user_id), INDEX IDX_BA388B74584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, parent_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_3AF346685E237E06 (name), INDEX IDX_3AF34668727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE coupons (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, discount NUMERIC(10, 2) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, UNIQUE INDEX UNIQ_F564111877153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE group_permissions (group_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_855D3AEFE54D947 (group_id), INDEX IDX_855D3AEFED90CCA (permission_id), PRIMARY KEY(group_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE order_details (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) NOT NULL, order_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_845CA2C18D9F6D38 (order_id), INDEX IDX_845CA2C14584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, total_amount NUMERIC(10, 2) NOT NULL, status VARCHAR(255) NOT NULL, shipping_fee NUMERIC(10, 2) DEFAULT 0 NOT NULL, discount NUMERIC(10, 2) DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, user_id INT NOT NULL, coupon_id INT DEFAULT NULL, INDEX IDX_E52FFDEEA76ED395 (user_id), INDEX IDX_E52FFDEE66C5951B (coupon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE permissions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_2DEDCC6F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, stock INT NOT NULL, unique_features LONGTEXT DEFAULT NULL, is_featured TINYINT(1) DEFAULT 0 NOT NULL, city VARCHAR(255) DEFAULT NULL, district VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, category_id INT DEFAULT NULL, INDEX IDX_B3BA5A5A12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE reviews (id INT AUTO_INCREMENT NOT NULL, rating INT NOT NULL, comment LONGTEXT DEFAULT NULL, is_approved TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, product_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6970EB0F4584665A (product_id), INDEX IDX_6970EB0FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user_group_members (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_2D2E2BA3A76ED395 (user_id), INDEX IDX_2D2E2BA3FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_953F224D5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user_permissions (user_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_84F605FAA76ED395 (user_id), INDEX IDX_84F605FAFED90CCA (permission_id), PRIMARY KEY(user_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, role VARCHAR(50) DEFAULT \'customer\' NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, user_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_9CE12A31A76ED395 (user_id), INDEX IDX_9CE12A314584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B74584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE group_permissions ADD CONSTRAINT FK_855D3AEFE54D947 FOREIGN KEY (group_id) REFERENCES user_groups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_permissions ADD CONSTRAINT FK_855D3AEFED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C14584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE66C5951B FOREIGN KEY (coupon_id) REFERENCES coupons (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F4584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_group_members ADD CONSTRAINT FK_2D2E2BA3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_members ADD CONSTRAINT FK_2D2E2BA3FE54D947 FOREIGN KEY (group_id) REFERENCES user_groups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permissions ADD CONSTRAINT FK_84F605FAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permissions ADD CONSTRAINT FK_84F605FAFED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A314584665A FOREIGN KEY (product_id) REFERENCES products (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B74584665A');
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE group_permissions DROP FOREIGN KEY FK_855D3AEFE54D947');
        $this->addSql('ALTER TABLE group_permissions DROP FOREIGN KEY FK_855D3AEFED90CCA');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C18D9F6D38');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C14584665A');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE66C5951B');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A12469DE2');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F4584665A');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FA76ED395');
        $this->addSql('ALTER TABLE user_group_members DROP FOREIGN KEY FK_2D2E2BA3A76ED395');
        $this->addSql('ALTER TABLE user_group_members DROP FOREIGN KEY FK_2D2E2BA3FE54D947');
        $this->addSql('ALTER TABLE user_permissions DROP FOREIGN KEY FK_84F605FAA76ED395');
        $this->addSql('ALTER TABLE user_permissions DROP FOREIGN KEY FK_84F605FAFED90CCA');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31A76ED395');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A314584665A');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE coupons');
        $this->addSql('DROP TABLE group_permissions');
        $this->addSql('DROP TABLE order_details');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('DROP TABLE user_group_members');
        $this->addSql('DROP TABLE user_groups');
        $this->addSql('DROP TABLE user_permissions');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE wishlist');
    }
}
