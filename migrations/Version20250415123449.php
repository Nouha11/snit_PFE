<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415123449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE designation ADD id_des INT AUTO_INCREMENT NOT NULL, ADD libelle VARCHAR(255) NOT NULL, DROP id, ADD PRIMARY KEY (id_des)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE designation MODIFY id_des INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON designation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE designation ADD id INT NOT NULL, DROP id_des, DROP libelle
        SQL);
    }
}
