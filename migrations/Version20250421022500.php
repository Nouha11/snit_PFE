<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421022500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB23AB0CAD FOREIGN KEY (Num_contrat) REFERENCES contrat (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D11814AB23AB0CAD ON intervention (Num_contrat)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3FFBDEDD0 FOREIGN KEY (id_des) REFERENCES direction (id_des)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB23AB0CAD
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D11814AB23AB0CAD ON intervention
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3FFBDEDD0
        SQL);
    }
}
