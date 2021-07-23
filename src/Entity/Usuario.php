<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UsuarioRepository::class)
 */
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Email del usuario.
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 2,
     *     max = 180,
     *     minMessage = "El email debe ser mínimo {{ limit }} caracteres de largo",
     *     maxMessage = "El email no debe tener más de {{ limit }} caracteres")
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 4,
     *     max = 255,
     *     minMessage = "La contraseña debe ser mínimo {{ limit }} caracteres de largo",
     *     maxMessage = "La contraseña no debe tener más de {{ limit }} caracteres")
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var integer
     * @ORM\Column(type="bigint")
     */
    private $cedula;

    /**
     * @var string Nombre del usuario.
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 2,
     *     max = 100,
     *     minMessage = "El nombre debe ser mínimo {{ limit }} caracteres de largo",
     *     maxMessage = "El nombre no debe tener más de {{ limit }} caracteres")
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
     * @var string Apellido del usuario.
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 2,
     *     max = 100,
     *     minMessage = "El apellido debe ser mínimo {{ limit }} caracteres de largo",
     *     maxMessage = "El apellido no debe tener más de {{ limit }} caracteres")
     * @ORM\Column(type="string", length=100)
     */
    private $apellido;

    /**
     * @var boolean
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * Usuario constructor.
     */
    public function __construct()
    {
        $this->id = 0;
        $this->cedula = 0;
        $this->nombre = "";
        $this->apellido = "";
        $this->email = "";
        $this->isActive = true;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Usuario
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return Usuario
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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
     * @return mixed
     */
    public function getCedula()
    {
        return $this->cedula;
    }

    /**
     * @param mixed $cedula
     */
    public function setCedula($cedula): void
    {
        $this->cedula = $cedula;
    }

    /**
     * @return mixed
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     */
    public function setNombre($nombre): void
    {
        $this->nombre = $nombre;
    }

    /**
     * @return mixed
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * @param mixed $apellido
     */
    public function setApellido($apellido): void
    {
        $this->apellido = $apellido;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }


}
