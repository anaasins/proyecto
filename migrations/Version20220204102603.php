<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220204102603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ejercicio (id INT AUTO_INCREMENT NOT NULL, autor_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) NOT NULL, fecha_creacion DATE NOT NULL, revisado TINYINT(1) NOT NULL, fecha_revision DATE DEFAULT NULL, aceptado TINYINT(1) DEFAULT NULL, disponible TINYINT(1) NOT NULL, documento VARCHAR(255) NOT NULL, imagen VARCHAR(255) NOT NULL, niveles_disponibles INT NOT NULL, INDEX IDX_95ADCFF414D45BBE (autor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ejercicio ADD CONSTRAINT FK_95ADCFF414D45BBE FOREIGN KEY (autor_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE entrenamiento ADD usuario_id INT NOT NULL, ADD ejercicio_id INT NOT NULL, ADD fecha DATE NOT NULL, ADD puntuacion INT NOT NULL, ADD nivel_alcanzado INT DEFAULT NULL');
        $this->addSql('ALTER TABLE entrenamiento ADD CONSTRAINT FK_677FFDA6DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE entrenamiento ADD CONSTRAINT FK_677FFDA630890A7D FOREIGN KEY (ejercicio_id) REFERENCES ejercicio (id)');
        $this->addSql('CREATE INDEX IDX_677FFDA6DB38439E ON entrenamiento (usuario_id)');
        $this->addSql('CREATE INDEX IDX_677FFDA630890A7D ON entrenamiento (ejercicio_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entrenamiento DROP FOREIGN KEY FK_677FFDA630890A7D');
        $this->addSql('DROP TABLE ejercicio');
        $this->addSql('ALTER TABLE entrenamiento DROP FOREIGN KEY FK_677FFDA6DB38439E');
        $this->addSql('DROP INDEX IDX_677FFDA6DB38439E ON entrenamiento');
        $this->addSql('DROP INDEX IDX_677FFDA630890A7D ON entrenamiento');
        $this->addSql('ALTER TABLE entrenamiento DROP usuario_id, DROP ejercicio_id, DROP fecha, DROP puntuacion, DROP nivel_alcanzado');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE rol CHANGE rol rol VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE usuario CHANGE nombre nombre VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE telefono telefono VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE correo correo VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
