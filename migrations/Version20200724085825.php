<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200724085825 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles ADD overview VARCHAR(255) NOT NULL, ADD vip TINYINT(1) NOT NULL, ADD `like` INT NOT NULL');
        $this->addSql('ALTER TABLE users ADD nom VARCHAR(255) NOT NULL, ADD membership_contract DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP overview, DROP vip, DROP `like`');
        $this->addSql('ALTER TABLE users DROP nom, DROP membership_contract');
    }
}
