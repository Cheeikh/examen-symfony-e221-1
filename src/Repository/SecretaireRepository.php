<?php

namespace App\Repository;

use App\Entity\Secretaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Secretaire>
 *
 * @method Secretaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Secretaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Secretaire[]    findAll()
 * @method Secretaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecretaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Secretaire::class);
    }

    public function save(Secretaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Secretaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 