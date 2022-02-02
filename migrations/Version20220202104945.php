<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220202104945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario ADD rol_id_id INT NOT NULL, ADD rol_id INT NOT NULL');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05D393FB813 FOREIGN KEY (rol_id_id) REFERENCES rol (id)');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05D4BAB96C FOREIGN KEY (rol_id) REFERENCES rol (id)');
        $this->addSql('CREATE INDEX IDX_2265B05D393FB813 ON usuario (rol_id_id)');
        $this->addSql('CREATE INDEX IDX_2265B05D4BAB96C ON usuario (rol_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entrenamiento CHANGE nombre nombre VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE descripcion descripcion VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE documento documento VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE imagen imagen VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE rol CHANGE rol rol VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05D393FB813');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05D4BAB96C');
        $this->addSql('DROP INDEX IDX_2265B05D393FB813 ON usuario');
        $this->addSql('DROP INDEX IDX_2265B05D4BAB96C ON usuario');
        $this->addSql('ALTER TABLE usuario DROP rol_id_id, DROP rol_id, CHANGE nombre nombre VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE telefono telefono VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE correo correo VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
