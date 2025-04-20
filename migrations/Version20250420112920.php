<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250420112920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_9CE12A31A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE wishlist_item (id INT AUTO_INCREMENT NOT NULL, wishlist_id INT NOT NULL, product_id INT NOT NULL, added_at INT NOT NULL, INDEX IDX_6424F4E8FB8E54CD (wishlist_id), INDEX IDX_6424F4E84584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wishlist_item ADD CONSTRAINT FK_6424F4E8FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wishlist_item ADD CONSTRAINT FK_6424F4E84584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wishlist_item DROP FOREIGN KEY FK_6424F4E8FB8E54CD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wishlist_item DROP FOREIGN KEY FK_6424F4E84584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE wishlist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE wishlist_item
        SQL);
    }
}
