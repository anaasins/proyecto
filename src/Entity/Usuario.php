<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[UniqueEntity(fields: ['correo'], message: 'There is already an account with this correo')]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $telefono;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $correo;

    #[ORM\Column(type: 'string', length: 255)]
    private $password;

    #[ORM\Column(type: 'string', nullable: true)]
    private $fecha_nacimiento;

    #[ORM\Column(type: 'date')]
    private $fecha_registro;

    #[ORM\ManyToOne(targetEntity: Rol::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private $rol;

    #[ORM\OneToMany(mappedBy: 'autor', targetEntity: Ejercicio::class)]
    private $ejercicios;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Entrenamiento::class, orphanRemoval: true)]
    private $entrens;

    #[ORM\Column(type: 'string', length: 255)]
    private $apellidos;

    /**
     * @ORM\Column(type="json")
     */
    #[ORM\Column(type: 'json')]
    private $roles = [];

    public function __construct()
    {
        $this->ejercicios = new ArrayCollection();
        $this->entrens = new ArrayCollection();
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

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): self
    {
        $this->correo = $correo;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFechaNacimiento(): ?string
    {
        return $this->fecha_nacimiento;
    }

    public function setFechaNacimiento(string $fecha_nacimiento): self
    {
        $this->fecha_nacimiento = $fecha_nacimiento;

        return $this;
    }

    public function getFechaRegistro(): ?\DateTimeInterface
    {
        return $this->fecha_registro;
    }

    public function setFechaRegistro(\DateTimeInterface $fecha_registro): self
    {
        $this->fecha_registro = $fecha_registro;

        return $this;
    }

    public function getRol(): ?Rol
    {
        return $this->rol;
    }

    public function setRol(?Rol $rol): self
    {
        $this->rol = $rol;
        $this->setRoles($this->roles);

        return $this;
    }

    /**
     * @return Collection|Ejercicio[]
     */
    public function getEjercicios(): Collection
    {
        return $this->ejercicios;
    }

    public function addEjercicio(Ejercicio $ejercicio): self
    {
        if (!$this->ejercicios->contains($ejercicio)) {
            $this->ejercicios[] = $ejercicio;
            $ejercicio->setAutor($this);
        }

        return $this;
    }

    public function removeEjercicio(Ejercicio $ejercicio): self
    {
        if ($this->ejercicios->removeElement($ejercicio)) {
            // set the owning side to null (unless already changed)
            if ($ejercicio->getAutor() === $this) {
                $ejercicio->setAutor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Entrenamiento[]
     */
    public function getEntrens(): Collection
    {
        return $this->entrens;
    }

    public function addEntren(Entrenamiento $entren): self
    {
        if (!$this->entrens->contains($entren)) {
            $this->entrens[] = $entren;
            $entren->setUsuario($this);
        }

        return $this;
    }

    public function removeEntren(Entrenamiento $entren): self
    {
        if ($this->entrens->removeElement($entren)) {
            // set the owning side to null (unless already changed)
            if ($entren->getUsuario() === $this) {
                $entren->setUsuario(null);
            }
        }

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->correo;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     * TODO: CAMBIAR-HO PER A QUE AGAFE ROL DE L'ALRTA TABLA.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        if(empty($roles)){
          $this->setRoles($roles);
          //$roles[] = "ROLE_USER";
        }
        if(!in_array('ROLE_USER', $roles)){
          array_push($roles, 'ROLE_USER');
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $rolD = $this->rol->getRol();
        $this->roles = $roles;
        array_push($this->roles, $rolD);

        return $this;
    }

}
