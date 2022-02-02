<?php

namespace App\Entity;

use App\Repository\RolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolRepository::class)]
class Rol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $rol;

    #[ORM\OneToMany(mappedBy: 'rol', targetEntity: Usuario::class)]
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(string $rol): self
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Usuario $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setRol($this);
        }

        return $this;
    }

    public function removeUser(Usuario $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRol() === $this) {
                $user->setRol(null);
            }
        }

        return $this;
    }
}
