<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202408211402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Contacts creation pages';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `contacts` (`id` bigint(20) NOT NULL, `fullname` varchar(255) NOT NULL, `email` varchar(255) NOT NULL, `phone_number` varchar(128) NOT NULL, `status` varchar(50) NOT NULL, `created_at` timestamp NULL DEFAULT current_timestamp(), `updated_at` timestamp NULL DEFAULT current_timestamp() ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
        $this->addSql('ALTER TABLE `contacts` CHANGE `id` `id` BIGINT(20) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`)');
        $this->addSql('ALTER TABLE `contacts` ADD `user_id` BIGINT NULL AFTER `id`');
        $this->addSql('ALTER TABLE `contacts` ADD CONSTRAINT `_create_relation_with_user` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contacts DROP FOREIGN KEY _create_relation_with_user');
        $this->addSql('ALTER TABLE `contacts` DROP `user_id`');
        $this->addSql('DROP TABLE `contacts`');
    }
}
