<?php

namespace App\Entity;

use App\Repository\EmpleadoRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\Column(type="bigint")
     */
    private $cedula;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
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
