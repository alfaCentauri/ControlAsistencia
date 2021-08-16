<?php

namespace App\Repository;

use App\Entity\Empleado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @method Empleado|null find($id, $lockMode = null, $lockVersion = null)
 * @method Empleado|null findOneBy(array $criteria, array $orderBy = null)
 * @method Empleado[]    findAll()
 * @method Empleado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpleadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Empleado::class);
    }

    /**
     * Este método permite contar todos los empleados.
     * @return int Cantidad total de empleados registrados en el sistema.
     */
    public function contarTodos(): int
    {
        return $this->createQueryBuilder('E')
            ->select('count(E.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Este método regresa una cantidad de registros indicados por el parametro $inicio.
     * @param int $inicio Indice de inicio de la busqueda.
     * @param int $fin Cantidad de registros ha buscar.
     * @return array Arreglo con el resultado de la busqueda.
     */
    public function paginarEmpleados($inicio, $fin): array
    {
        return $this->createQueryBuilder('E')
            ->orderBy('E.nombre', 'ASC')
            ->setFirstResult($inicio)
            ->setMaxResults($fin)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param string $value
     * @return Empleado[] Returns an array of Empleado objects
     */
    public function buscar(string $value, int $inicio = 1, int $fin = 10): array
    {
        $resultado = $this->getEntityManager()
            ->createQuery('SELECT E FROM App:Empleado E where '
                .'E.nombre like \'%'.$value.'%\' or E.apellido like \'%'
                .$value.'%\' ORDER BY E.cedula ASC')
            ->setFirstResult($inicio)
            ->setMaxResults($fin)
            ->getResult();
        return $resultado;
    }

    /**
     * @param $value
     * @return Empleado|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByCedula($value): ?Empleado
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.cedula = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Empleado[] Returns an array of Empleado objects
     */
    public function listarTodos(): array
    {
        return $this->createQueryBuilder('E')
            ->orderBy('E.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $value
     * @return int Returns an integer with number of Empleado objects
     */
    public function contarEmpleadosBuscados(string $value): int
    {
        return $this->createQueryBuilder('E')
            ->select('count(E.id)')
            ->where('E.nombre like \'%'.$value.'%\' ')
            ->orWhere('E.apellido like \'%'.$value.'%\' ')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
