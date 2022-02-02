<?php

namespace App\Entity;

use App\Repository\EntrenamientoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrenamientoRepository::class)]
class Entrenamiento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 255)]
    private $descripcion;

    #[ORM\Column(type: 'integer')]
    private $autor;

    #[ORM\Column(type: 'date')]
    private $fecha_creacion;

    #[ORM\Column(type: 'boolean')]
    private $revisado;

    #[ORM\Column(type: 'date', nullable: true)]
    private $fecha_revision;

    #[ORM\Column(type: 'boolean')]
    private $aceptado;

    #[ORM\Column(type: 'boolean')]
    private $disponible;

    #[ORM\Column(type: 'string', length: 255)]
    private $documento;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $imagen;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getAutor(): ?int
    {
        return $this->autor;
    }

    public function setAutor(int $autor): self
    {
        $this->autor = $autor;

        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fecha_creacion;
    }

    public function setFechaCreacion(\DateTimeInterface $fecha_creacion): self
    {
        $this->fecha_creacion = $fecha_creacion;

        return $this;
    }

    public function getRevisado(): ?bool
    {
        return $this->revisado;
    }

    public function setRevisado(bool $revisado): self
    {
        $this->revisado = $revisado;

        return $this;
    }

    public function getFechaRevision(): ?\DateTimeInterface
    {
        return $this->fecha_revision;
    }

    public function setFechaRevision(?\DateTimeInterface $fecha_revision): self
    {
        $this->fecha_revision = $fecha_revision;

        return $this;
    }

    public function getAceptado(): ?bool
    {
        return $this->aceptado;
    }

    public function setAceptado(bool $aceptado): self
    {
        $this->aceptado = $aceptado;

        return $this;
    }

    public function getDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): self
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function setDocumento(string $documento): self
    {
        $this->documento = $documento;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }
}
