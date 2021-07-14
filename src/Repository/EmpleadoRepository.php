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

    // /**
    //  * @return Empleado[] Returns an array of Empleado objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
     * Encuentra un empleado por su número de cedula.
     * @param int $cedula
     * @return Empleado|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByCedula(int $cedula): ?Empleado
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.cedula = :val')
            ->setParameter('val', $cedula)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
