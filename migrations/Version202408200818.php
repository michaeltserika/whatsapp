<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202408200818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add relationship between campaign and user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user CHANGE id id BIGINT(11) NOT NULL AUTO_INCREMENT');
        $this->addSql('ALTER TABLE campaign ADD user_id BIGINT NULL AFTER id');
        $this->addSql('ALTER TABLE campaign ADD INDEX(user_id)');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT relation_between_campaign_user FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE RESTRICT ON UPDATE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user CHANGE id id INT(11) NOT NULL AUTO_INCREMENT');
        $this->addSql('ALTER TABLE campaign DROP user_id');
        $this->addSql('ALTER TABLE campaign DROP INDEX user_id');
        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY relation_between_campaign_user');
    }
}
