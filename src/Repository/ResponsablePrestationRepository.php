<?php

namespace App\Repository;

use App\Entity\ResponsablePrestation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResponsablePrestation>
 *
 * @method ResponsablePrestation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResponsablePrestation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResponsablePrestation[]    findAll()
 * @method ResponsablePrestation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponsablePrestationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResponsablePrestation::class);
    }

    public function save(ResponsablePrestation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ResponsablePrestation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 