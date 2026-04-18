<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240423193317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE cart_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE cart (id INT NOT NULL, _user_id INT NOT NULL, _product_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BA388B7D32632E8 ON cart (_user_id)');
        $this->addSql('CREATE INDEX IDX_BA388B726CF9622 ON cart (_product_id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7D32632E8 FOREIGN KEY (_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B726CF9622 FOREIGN KEY (_product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX idx_f52993989d86650f RENAME TO IDX_F5299398A76ED395');
        $this->addSql('ALTER INDEX idx_d34a04ad9d86650f RENAME TO IDX_D34A04ADA76ED395');
        $this->addSql('ALTER INDEX idx_d34a04adfcdaeaaa RENAME TO IDX_D34A04AD8D9F6D38');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE cart_id_seq CASCADE');
        $this->addSql('ALTER TABLE cart DROP CONSTRAINT FK_BA388B7D32632E8');
        $this->addSql('ALTER TABLE cart DROP CONSTRAINT FK_BA388B726CF9622');
        $this->addSql('DROP TABLE cart');
        $this->addSql('ALTER INDEX idx_d34a04ad8d9f6d38 RENAME TO idx_d34a04adfcdaeaaa');
        $this->addSql('ALTER INDEX idx_d34a04ada76ed395 RENAME TO idx_d34a04ad9d86650f');
        $this->addSql('ALTER INDEX idx_f5299398a76ed395 RENAME TO idx_f52993989d86650f');
    }
}
