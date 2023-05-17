<?php

namespace App\Repository;

use App\Entity\Fichier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Fichier>
 *
 * @method Fichier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fichier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fichier[]    findAll()
 * @method Fichier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FichierRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 10;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fichier::class);
    }

    public function save(Fichier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Fichier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getFichierPaginator(int $offset, array $search = null)
    {
        $query = $this->createQueryBuilder('c');

        if($search != null && count($search) > 0){
            foreach($search as $req){
                if($req == "date" || $req == "site"){
                    $query->join('c.'.$req, $req)
                    ->addOrderBy( $req.".id", 'DESC');
                } 
                elseif($req == "traduction" || $req == "spin" || $req == "liste") {
                    $query->Where( "c.type LIKE :req")
                    ->setParameter('req' , $req);
                } else {
                    $query->join('c.site', "site")
                    ->Where('site.nom LIKE :nom')
                    ->setParameter('nom', '%' . $req . '%');
                }
            }
        }

        $query->addOrderBy('c.id', 'DESC')
        ->AndWhere('c.outils = 1 ')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }


    
    public function getFichierPaginatorExtracteurImg(int $offset, array $search = null)
    {
        $query = $this->createQueryBuilder('c');

        if($search != null && count($search) > 0){
            foreach($search as $req){
                if($req == "date" || $req == "site"){
                    $query->join('c.'.$req, $req)
                    ->addOrderBy( $req.".id", 'DESC');
                } else {
                    $query->join('c.site', "site")
                    ->Where('site.nom LIKE :nom')
                    ->setParameter('nom', '%' . $req . '%');
                }
            }
        }

        $query->addOrderBy('c.id', 'DESC')
        ->AndWhere('c.outils = 2 ')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }
    //    /**
    //     * @return Fichier[] Returns an array of Fichier objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Fichier
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
