<?php

namespace App\Entity;

use App\Repository\EjercicioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EjercicioRepository::class)]
class Ejercicio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 255)]
    private $descripcion;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'ejercicios')]
    #[ORM\JoinColumn(nullable: false)]
    private $autor;

    #[ORM\Column(type: 'date')]
    private $fecha_creacion;

    #[ORM\Column(type: 'boolean')]
    private $revisado;

    #[ORM\Column(type: 'date', nullable: true)]
    private $fecha_revision;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $aceptado;

    #[ORM\Column(type: 'boolean')]
    private $disponible;

    #[ORM\Column(type: 'string', length: 255)]
    private $documento;

    #[ORM\Column(type: 'string', length: 255)]
    private $imagen;

    #[ORM\Column(type: 'integer')]
    private $niveles_disponibles;

    #[ORM\OneToMany(mappedBy: 'ejercicio', targetEntity: Entrenamiento::class, orphanRemoval: true)]
    private $entrenamientos;

    public function __construct()
    {
        $this->entrenamientos = new ArrayCollection();
    }

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

    public function getAutor(): ?Usuario
    {
        return $this->autor;
    }

    public function setAutor(?Usuario $autor): self
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

    public function setAceptado(?bool $aceptado): self
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

    public function setImagen(string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getNivelesDisponibles(): ?int
    {
        return $this->niveles_disponibles;
    }

    public function setNivelesDisponibles(int $niveles_disponibles): self
    {
        $this->niveles_disponibles = $niveles_disponibles;

        return $this;
    }

    /**
     * @return Collection|Entrenamiento[]
     */
    public function getEntrenamientos(): Collection
    {
        return $this->entrenamientos;
    }

    public function addEntrenamiento(Entrenamiento $entrenamiento): self
    {
        if (!$this->entrenamientos->contains($entrenamiento)) {
            $this->entrenamientos[] = $entrenamiento;
            $entrenamiento->setEjercicio($this);
        }

        return $this;
    }

    public function removeEntrenamiento(Entrenamiento $entrenamiento): self
    {
        if ($this->entrenamientos->removeElement($entrenamiento)) {
            // set the owning side to null (unless already changed)
            if ($entrenamiento->getEjercicio() === $this) {
                $entrenamiento->setEjercicio(null);
            }
        }

        return $this;
    }
}
