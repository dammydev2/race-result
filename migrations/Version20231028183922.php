<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231028183922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE race MODIFY created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP, MODIFY updated_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, MODIFY overall_placement INT NULL, MODIFY age_category_placement INT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE race MODIFY created_at DATETIME, MODIFY updated_at DATETIME');
    }
}
