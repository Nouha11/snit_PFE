<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421021129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE intervention (id INT AUTO_INCREMENT NOT NULL, type_intervention VARCHAR(50) NOT NULL, num_contrat VARCHAR(100) DEFAULT NULL, date_intervention DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, Equipement_id INT DEFAULT NULL, INDEX IDX_D11814AB331144A9 (Equipement_id), INDEX IDX_D11814AB23AB0CAD (Num_contrat), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB331144A9 FOREIGN KEY (Equipement_id) REFERENCES equipement (id_eq)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB23AB0CAD FOREIGN KEY (Num_contrat) REFERENCES contrat (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB331144A9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB23AB0CAD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE intervention
        SQL);
    }
}
