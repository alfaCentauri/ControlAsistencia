<?php

namespace App\Repository;

use App\Entity\Empleado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
     * @return integer Cantidad total de empleados registrados en el sistema.
     */
    public function contarTodos(){
        $qb = $this->getEntityManager()->createQueryBuilder('empleado');
        return $qb->select($qb->expr()->count('empleado.id'))
            ->from('Empleado','empleado')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Este método regresa una cantidad de registros indicados por el parametro $inicio.
     * @param int $inicio Indice de inicio de la busqueda.
     * @param int $fin Cantidad de registros ha buscar.
     * @return array Arreglo con el resultado de la busqueda.
     */
    public function paginarEmpleados($inicio, $fin){
        $resultado = $this->getEntityManager()
            ->createQuery('SELECT E FROM Empleado E ORDER BY E.cedula ASC')
            ->setFirstResult($inicio)
            ->setMaxResults($fin)
            ->getResult();
        return $resultado;
    }

     /**
      * @return Empleado[] Returns an array of Empleado objects
      */
    public function buscar($value)
    {
        return $this->createQueryBuilder('e')
            ->where('e.cedula like :%val%')
            ->andWhere('e.nombre like :%val%')
            ->andWhere('e.apellido like :%val%')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
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
}
