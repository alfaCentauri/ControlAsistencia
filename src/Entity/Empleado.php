<?php

namespace App\Entity;

use App\Repository\EmpleadoRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EmpleadoRepository::class)
 */
class Empleado
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(type="bigint")
     */
    private $cedula;

    /**
     * @var string Nombre del empleado.
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
     * @var string Apellido del empleado.
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
     * @var Asistencia
     * @ORM\OneToMany(targetEntity="Asistencia", mappedBy="empleado")
     */
    private $asistencias;

    /**
     * Empleado constructor.
     */
    public function __construct()
    {
        $this->asistencias = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCedula(): ?int
    {
        return $this->cedula;
    }

    public function setCedula(int $cedula): self
    {
        $this->cedula = $cedula;

        return $this;
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

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * @return Asistencia
     */
    public function getAsistencias(): Asistencia
    {
        return $this->asistencias;
    }

    /**
     * @param Asistencia $asistencias
     */
    public function setAsistencias(Asistencia $asistencias): void
    {
        $this->asistencias = $asistencias;
    }

}
