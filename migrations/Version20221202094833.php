<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221202094833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_trick_id INT NOT NULL, pseudo VARCHAR(50) NOT NULL, comment LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5F9E962A79F37AE5 (id_user_id), INDEX IDX_5F9E962AE25A52BB (id_trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, id_trick_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6AE25A52BB (id_trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE videos (id INT AUTO_INCREMENT NOT NULL, id_trick_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_29AA6432E25A52BB (id_trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A79F37AE5 FOREIGN KEY (id_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AE25A52BB FOREIGN KEY (id_trick_id) REFERENCES tricks (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AE25A52BB FOREIGN KEY (id_trick_id) REFERENCES tricks (id)');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA6432E25A52BB FOREIGN KEY (id_trick_id) REFERENCES tricks (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E1D902C15E237E06 ON tricks (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A79F37AE5');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AE25A52BB');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AE25A52BB');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA6432E25A52BB');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE videos');
        $this->addSql('DROP INDEX UNIQ_E1D902C15E237E06 ON tricks');
    }
}
