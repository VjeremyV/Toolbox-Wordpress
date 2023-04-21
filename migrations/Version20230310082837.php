<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230310082837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE date (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outils (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outils_users (outils_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_D4B94E2EAF7E699 (outils_id), INDEX IDX_D4B94E2E67B3B43D (users_id), PRIMARY KEY(outils_id, users_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE outils_users ADD CONSTRAINT FK_D4B94E2EAF7E699 FOREIGN KEY (outils_id) REFERENCES outils (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outils_users ADD CONSTRAINT FK_D4B94E2E67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichier ADD outils_id INT NOT NULL, ADD date_id INT NOT NULL, ADD type VARCHAR(255) NOT NULL, DROP date_upload');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551FAF7E699 FOREIGN KEY (outils_id) REFERENCES outils (id)');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551FB897366B FOREIGN KEY (date_id) REFERENCES date (id)');
        $this->addSql('CREATE INDEX IDX_9B76551FAF7E699 ON fichier (outils_id)');
        $this->addSql('CREATE INDEX IDX_9B76551FB897366B ON fichier (date_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551FB897366B');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551FAF7E699');
        $this->addSql('ALTER TABLE outils_users DROP FOREIGN KEY FK_D4B94E2EAF7E699');
        $this->addSql('ALTER TABLE outils_users DROP FOREIGN KEY FK_D4B94E2E67B3B43D');
        $this->addSql('DROP TABLE date');
        $this->addSql('DROP TABLE outils');
        $this->addSql('DROP TABLE outils_users');
        $this->addSql('DROP INDEX IDX_9B76551FAF7E699 ON fichier');
        $this->addSql('DROP INDEX IDX_9B76551FB897366B ON fichier');
        $this->addSql('ALTER TABLE fichier ADD date_upload DATETIME NOT NULL, DROP outils_id, DROP date_id, DROP type');
    }
}
