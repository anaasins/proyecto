<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220406092929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ejercicio ADD fecha_revision DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ejercicio DROP fecha_revision, CHANGE nombre nombre VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE descripcion descripcion VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE documento documento VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE imagen imagen VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE instrucciones instrucciones VARCHAR(1024) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE documentos_extra documentos_extra LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE rol CHANGE rol rol VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE usuario CHANGE nombre nombre VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE telefono telefono VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE correo correo VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE fecha_nacimiento fecha_nacimiento VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE apellidos apellidos VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
    }
}