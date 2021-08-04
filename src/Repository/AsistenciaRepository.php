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
    public function contarTodas(): int
    {
        $entityManager = $this->getEntityManager();
        try{
            $resultado = $entityManager->createQuery('SELECT count(a.id) FROM App\Entity\Asistencia a')
                ->getSingleScalarResult();
        }catch(NoResultException $e){
            return 0;
        }
        return $resultado;
    }

    /**
     * Este método permite contar todas las asistencias de los empleados para un mes especifico.
     * @param string $mes
     * @return int Cantidad total de asistencias en el mes indicado en el sistema.
     */
    public function contarTodasAsistenciasMes(string $mes): int
    {
        $entityManager = $this->getEntityManager();
        $resultado = 0;
        try{
            $resultado = $entityManager
                ->createQuery('SELECT count(a.id) FROM App\Entity\Asistencia a WHERE a.fecha LIKE \'%'.$mes.'%\' ')
                ->getSingleScalarResult();
        }catch(NoResultException $e){
            return 0;
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
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'ASC')
            ->setFirstResult($inicio)
            ->setMaxResults($fin)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param string $mes
     * @return array Contiene la lista de todas las asistencias del mes indicado.
     */
    public function listarAsistencias(string $mes, int $inicio = 1, int $fin = 10): array
    {
        $resultado = array();
        $entityManager = $this->getEntityManager();
        try{
            $resultado = $entityManager
                ->createQuery('SELECT a FROM App\Entity\Asistencia a WHERE a.fecha LIKE \'%'.$mes.'%\' ')
                ->setFirstResult($inicio)
                ->setMaxResults($fin)
                ->getResult();
        }catch(NoResultException $e){
            return array();
        }
        return $resultado;
    }

    /**
     * @param string $mes Mes y año a buscar.
     * @param string $cadena Nombre o Apellido del empleado.
     * @return array Contiene la lista de todas las asistencias del mes indicado.
     */
    public function buscar(string $mes, string $cadena, int $inicio = 1, int $fin = 10): array
    {
        $resultado = array();
        $entityManager = $this->getEntityManager();
        try{
            $resultado = $entityManager
                ->createQuery('SELECT a FROM App\Entity\Asistencia a WHERE a.fecha LIKE \'%'.$mes.'%\' ')
                ->setFirstResult($inicio)
                ->setMaxResults($fin)
                ->getResult();
        }catch(NoResultException $e){
            return array();
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
