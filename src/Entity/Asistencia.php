<?php

namespace App\Entity;

use App\Repository\AsistenciaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AsistenciaRepository::class)
 */
class Asistencia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $empleadoId;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="time")
     */
    private $horaEntrada;

    /**
     * @ORM\Column(type="time")
     */
    private $horaSalida;

    /**
     * @var Empleado
     * @ORM\ManyToOne(targetEntity="Empleado", inversedBy="asistencias")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empleado_id", referencedColumnName="id")
     * })
     */
    private $empleado;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmpleadoId(): ?int
    {
        return $this->empleadoId;
    }

    public function setEmpleadoId(int $empleadoId): self
    {
        $this->empleadoId = $empleadoId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

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

    public function getHoraEntrada(): ?\DateTimeInterface
    {
        return $this->horaEntrada;
    }

    public function setHoraEntrada(\DateTimeInterface $horaEntrada): self
    {
        $this->horaEntrada = $horaEntrada;

        return $this;
    }

    public function getHoraSalida(): ?\DateTimeInterface
    {
        return $this->horaSalida;
    }

    public function setHoraSalida(\DateTimeInterface $horaSalida): self
    {
        $this->horaSalida = $horaSalida;

        return $this;
    }

    /**
     * @return Empleado
     */
    public function getEmpleado(): Empleado
    {
        return $this->empleado;
    }

    /**
     * @param Empleado $empleado
     */
    public function setEmpleado(Empleado $empleado): void
    {
        $this->empleado = $empleado;
    }

}
