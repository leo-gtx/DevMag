<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200912194853 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles ADD prev_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168B168B8C0 FOREIGN KEY (prev_id) REFERENCES articles (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BFDD3168B168B8C0 ON articles (prev_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168B168B8C0');
        $this->addSql('DROP INDEX UNIQ_BFDD3168B168B8C0 ON articles');
        $this->addSql('ALTER TABLE articles DROP prev_id');
    }
}
