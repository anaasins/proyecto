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

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'entrens')]
    #[ORM\JoinColumn(nullable: false)]
    private $usuario;

    #[ORM\ManyToOne(targetEntity: Ejercicio::class, inversedBy: 'entrenamientos')]
    #[ORM\JoinColumn(nullable: false)]
    private $ejercicio;

    #[ORM\Column(type: 'date')]
    private $fecha;

    #[ORM\Column(type: 'integer')]
    private $puntuacion;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nivel_alcanzado;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getEjercicio(): ?Ejercicio
    {
        return $this->ejercicio;
    }

    public function setEjercicio(?Ejercicio $ejercicio): self
    {
        $this->ejercicio = $ejercicio;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getPuntuacion(): ?int
    {
        return $this->puntuacion;
    }

    public function setPuntuacion(int $puntuacion): self
    {
        $this->puntuacion = $puntuacion;

        return $this;
    }

    public function getNivelAlcanzado(): ?int
    {
        return $this->nivel_alcanzado;
    }

    public function setNivelAlcanzado(?int $nivel_alcanzado): self
    {
        $this->nivel_alcanzado = $nivel_alcanzado;

        return $this;
    }
}
