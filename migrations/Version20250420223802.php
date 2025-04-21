<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250420223802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE contrat (id INT AUTO_INCREMENT NOT NULL, num_contrat VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, date_enregistrement DATETIME NOT NULL, num_enregistrement VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipement ADD contrat_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipement ADD CONSTRAINT FK_B8B4C6F31823061F FOREIGN KEY (contrat_id) REFERENCES contrat (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B8B4C6F31823061F ON equipement (contrat_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE equipement DROP FOREIGN KEY FK_B8B4C6F31823061F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE contrat
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B8B4C6F31823061F ON equipement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipement DROP contrat_id
        SQL);
    }
}
