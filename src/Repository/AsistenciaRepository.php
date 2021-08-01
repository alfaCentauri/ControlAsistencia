<?php

namespace App\Repository;

use App\Entity\Asistencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Asistencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Asistencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Asistencia[]    findAll()
 * @method Asistencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AsistenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asistencia::class);
    }

    /**
     * Este método permite contar todas las asistencias de los empleados.
     * @return integer Cantidad total de asistencias en el sistema.
     */
    public function contarTodas(){
        $entityManager = $this->getEntityManager();
        try{
            $resultado = $entityManager->createQuery('SELECT count(a.id) FROM App\Entity\Asistencia a')
                ->getScalarResult();
        }catch(NoResultException $e){
            return null;
        }
        return $resultado;
    }

    /**
     * Este método permite contar todas las asistencias de los empleados para un mes especifico.
     * @param string $mes
     * @return mixed[]|null Cantidad total de asistencias en el sistema.
     */
    public function contarTodasAsistenciasMes(string $mes){
        $entityManager = $this->getEntityManager();
        try{
            $resultado = $entityManager->createQuery('SELECT count(a.id) FROM App\Entity\Asistencia a WHERE a.fecha LIKE %:mes% ')
                ->setParameter('mes', $mes)
                ->getScalarResult();
        }catch(NoResultException $e){
            return null;
        }
        return $resultado;
    }

    /**
     * Este método regresa una cantidad de registros indicados por el parametro $inicio.
     * @param int $inicio Indice de inicio de la busqueda.
     * @param int $fin Cantidad de registros ha buscar.
     * @return array Arreglo con el resultado de la busqueda.
     */
    public function paginarAsistencias($inicio, $fin){
        $resultado = $this->getEntityManager()
            ->createQuery('SELECT a FROM Asistencia a ORDER BY a.id ASC')
            ->setFirstResult($inicio)
            ->setMaxResults($fin)
            ->getResult();
        return $resultado;
    }

    /**
     * @param string $mes
     * @return \Doctrine\ORM\Query|null Contiene la lista de todas las asistencias del mes indicado.
     */
    public function listarAsistencias(string $mes){
        try{
            $resultado = $this->createQueryBuilder('a')
                ->select('a')
                ->where('a.fecha like %:mes%')
                ->setParameter('mes', $mes)
                ->orderBy('a.nombre', 'ASC')
                ->getQuery();
        }catch(NoResultException $e){
            return null;
        }
        return $resultado;
    }

    // /**
    //  * @return Asistencia[] Returns an array of Asistencia objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Asistencia
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
