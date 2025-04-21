<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421025845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD equipement_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id_eq)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D11814AB806F0F5C ON intervention (equipement_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB806F0F5C
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D11814AB806F0F5C ON intervention
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE intervention DROP equipement_id
        SQL);
    }
}
