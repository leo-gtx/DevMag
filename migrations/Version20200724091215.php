<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200724091215 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles ADD bookmakers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168D24BA748 FOREIGN KEY (bookmakers_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_BFDD3168D24BA748 ON articles (bookmakers_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168D24BA748');
        $this->addSql('DROP INDEX IDX_BFDD3168D24BA748 ON articles');
        $this->addSql('ALTER TABLE articles DROP bookmakers_id');
    }
}
