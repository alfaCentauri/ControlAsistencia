<?php

namespace App\Repository;

use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Usuario) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Este método permite buscar un usuario o usuarios que contengan una
     * palabra o cadena en cualquiera de sus campos.
     * @return resultset Regresa una lista de usuarios.
     */
    public function buscarUsuarios(string $cadena){
        $resultado = $this->getEntityManager()
            ->createQuery('SELECT u FROM App:Usuario u where '
                .'u.nombre like \'%'.$cadena.'%\' or u.apellido like \'%'
                .$cadena.'%\' or u.email like \'%'.$cadena.'%\' '
                . 'ORDER BY u.cedula ASC')
            ->getResult();
        return $resultado;
    }

    /**
     * Este método regresa una cantidad de registros indicados por el parametro
     * $fin desde la posición del parametro $inicio.
     * @param int $inicio
     * @param int $fin
     * @return Usuario[] Returns an array of Usuario objects
     */
    public function paginarUsuarios(int $inicio, int $fin){
        return $this->createQueryBuilder('u')
            ->orderBy('u.nombre', 'ASC')
            ->setFirstResult($inicio)
            ->setMaxResults($fin)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Cuenta la cantidad de registros almacenados.
     * @return int Cantidad total de usuarios.
     */
    public function contarTodos(): int
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    /*
    public function findOneBySomeField($value): ?Usuario
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
