<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230106205221 extends AbstractMigration
{

    // Если false, то есть при транзанкции миграции доктрин не будет его использовать
    public function isTransactional(): bool
    {
        return false;
    }



    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX CONCURRENTLY tweet__author_id__ind ON "tweet" (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX tweet__author_id__ind');
    }
}
