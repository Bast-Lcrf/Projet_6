<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221217094708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA6432E25A52BB');
        $this->addSql('DROP INDEX IDX_29AA6432E25A52BB ON videos');
        $this->addSql('ALTER TABLE videos ADD trick_id INT NOT NULL, DROP id_trick_id');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA6432B281BE2E FOREIGN KEY (trick_id) REFERENCES tricks (id)');
        $this->addSql('CREATE INDEX IDX_29AA6432B281BE2E ON videos (trick_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA6432B281BE2E');
        $this->addSql('DROP INDEX IDX_29AA6432B281BE2E ON videos');
        $this->addSql('ALTER TABLE videos ADD id_trick_id INT DEFAULT NULL, DROP trick_id');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA6432E25A52BB FOREIGN KEY (id_trick_id) REFERENCES tricks (id)');
        $this->addSql('CREATE INDEX IDX_29AA6432E25A52BB ON videos (id_trick_id)');
    }
}
